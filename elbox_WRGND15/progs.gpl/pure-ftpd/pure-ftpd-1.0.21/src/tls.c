#include <config.h>

#ifdef WITH_TLS
# ifndef IN_TLS_C
#  define IN_TLS_C 1
# endif

# include "ftpd.h"
# include "tls.h"
# include "ftpwho-update.h"
# include "messages.h"

static void tls_error(void) 
{
    logfile(LOG_ERR, "SSL/TLS [%s]: %s", 
            TLS_CERTIFICATE_FILE,
            ERR_error_string(ERR_get_error(), NULL));
    _EXIT(EXIT_FAILURE);
}

static int tls_init_diffie(void)
{
    DH *dh;
    BIO *bio;

    if ((bio = BIO_new_file(TLS_CERTIFICATE_FILE, "r")) == NULL) {
        return -1;
    }
    if ((dh = PEM_read_bio_DHparams(bio, NULL, NULL
#if OPENSSL_VERSION_NUMBER >= 0x00904000L
                                    , NULL
#endif
                                    )) == NULL) {
        BIO_free(bio);

        return 1;
    }
    if (SSL_CTX_set_tmp_dh(tls_ctx, dh) != 1) {
        return -1;
    }
    DH_free(dh);    
    BIO_free(bio);

    return 0;
}

static void tls_init_cache(void)
{
    SSL_CTX_set_session_cache_mode(tls_ctx, SSL_SESS_CACHE_OFF);
}

int tls_init_library(void) 
{
    unsigned int rnd;
    
    SSL_library_init();
    SSL_load_error_strings();
    while (RAND_status() == 0) {
        rnd = zrand();
        RAND_seed(&rnd, (int) sizeof rnd);
    }
    if ((tls_ctx = SSL_CTX_new(SSLv23_server_method())) == NULL) {
        tls_error();
    }
    tls_init_cache();
    SSL_CTX_set_options(tls_ctx, SSL_OP_ALL);    
    if (SSL_CTX_use_certificate_chain_file
        (tls_ctx, TLS_CERTIFICATE_FILE) != 1) {
        die(421, LOG_ERR,
            MSG_FILE_DOESNT_EXIST ": [%s]", TLS_CERTIFICATE_FILE);
    }
    if (SSL_CTX_use_PrivateKey_file(tls_ctx, TLS_CERTIFICATE_FILE,
                                    SSL_FILETYPE_PEM) != 1) {
        tls_error();
    }
    if (SSL_CTX_check_private_key(tls_ctx) != 1) {
        tls_error();
    }
    if (SSL_CTX_need_tmp_RSA(tls_ctx)) {
        RSA *ephemeral_key;

        if ((ephemeral_key = RSA_generate_key(RSA_EPHEMERAL_KEY_LEN, 
                                              RSA_F4, NULL, NULL)) == NULL) {
            tls_error();
        }
        if (SSL_CTX_set_tmp_rsa(tls_ctx, ephemeral_key) != 1) {
            tls_error();
        }
        RSA_free(ephemeral_key);
    }
    if (tls_init_diffie() < 0) {
        tls_error();
    }
#ifdef REQUIRE_VALID_CLIENT_CERTIFICATE
    SSL_CTX_set_verify(tls_ctx, SSL_VERIFY_FAIL_IF_NO_PEER_CERT |
                       SSL_VERIFY_PEER | SSL_VERIFY_CLIENT_ONCE, NULL);
#endif
    
    return 0;
}

void tls_free_library(void)
{
    if (tls_cnx != NULL) {
        SSL_free(tls_cnx);
        tls_cnx = NULL;
    }
    if (tls_ctx != NULL) {
        SSL_CTX_free(tls_ctx);
        tls_ctx = NULL;
    }
}

int tls_init_new_session(void)
{
    SSL_CIPHER *cipher;
    
    if (tls_ctx == NULL ||
        (tls_cnx = SSL_new(tls_ctx)) == NULL) {
        tls_error();
    }
    if (SSL_set_rfd(tls_cnx, 0) != 1 ||
        SSL_set_wfd(tls_cnx, 1) != 1) {
        tls_error();
    }
    SSL_set_accept_state(tls_cnx);
    if (SSL_accept(tls_cnx) <= 0) {
        tls_error();
    }
    if ((cipher = SSL_get_current_cipher(tls_cnx)) != NULL) {
        int alg_bits;
        int bits = SSL_CIPHER_get_bits(cipher, &alg_bits);
        
        if (alg_bits < bits) {
            bits = alg_bits;
        }
        logfile(LOG_INFO, MSG_TLS_INFO, SSL_CIPHER_get_version(cipher), 
                SSL_CIPHER_get_name(cipher), bits);
        if (bits < MINIMAL_CIPHER_KEY_LEN) {
            die(534, LOG_ERR, MSG_TLS_WEAK);
        }
    }
    return 0;
}

#endif
