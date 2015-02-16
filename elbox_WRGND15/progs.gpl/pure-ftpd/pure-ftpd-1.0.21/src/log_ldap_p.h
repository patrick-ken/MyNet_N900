
/* LDAP posixAccount handler for Pure-FTPd */
/* (C)opyleft 2001-2006 by Frank DENIS <j@pureftpd.org> */

#ifndef __LOG_LDAP_P_H__
#define __LOG_LDAP_P_H__ 1

#include <lber.h>
#include <ldap.h>

static char *ldap_host;
static char *port_s;
static int port;
static char *root;
static char *pwd;
static char *base;
static char *ldap_filter;
static char *ldap_homedirectory;
static char *ldap_version_s;
static int ldap_version;
static char *default_uid_s;
static uid_t default_uid;
static char *default_gid_s;
static gid_t default_gid;

static ConfigKeywords ldap_config_keywords[] = {
    { "LDAPServer", &ldap_host },
    { "LDAPPort", &port_s },    
    { "LDAPBindDN", &root },        
    { "LDAPBindPW", &pwd },
    { "LDAPBaseDN", &base },
    { "LDAPFilter", &ldap_filter},
    { "LDAPHomeDir", &ldap_homedirectory },
    { "LDAPVersion", &ldap_version_s },
    { "LDAPDefaultUID", &default_uid_s },
    { "LDAPDefaultGID", &default_gid_s },
    { NULL, NULL }
};

#endif
