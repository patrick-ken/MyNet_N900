#include <config.h>

#ifdef WITH_OSX_BONJOUR
# include "osx-extensions.h"
# include <CoreServices/CoreServices.h>

# ifdef WITH_DMALLOC
#  include <dmalloc.h>
# endif

# pragma mark 
# pragma mark *********** Bonjour Globals ***********
/* reg_reply -- empty callback function for DNSServiceRegistrationCreate() */
static void reg_reply(DNSServiceRegistrationReplyErrorType errorCode,
                      void *context)
{
    (void) errorCode;
    (void) context;
}

void doregistration(const char *name, unsigned long port)
{
    DNSServiceRegistrationCreate(name,
                                 "_ftp._tcp.",
                                 "",
                                 port,
                                 "",
                                 (DNSServiceRegistrationReply) reg_reply,
                                 NULL);
}

# pragma mark 
# pragma mark *********** Notification ***********
void refreshManager(void)
{
    CFStringRef observedObject = CFSTR("org.pureftpd.osx");
    CFNotificationCenterRef center =
        CFNotificationCenterGetDistributedCenter();
    CFNotificationCenterPostNotification(center,
                                         CFSTR("refreshStatus"),
                                         observedObject,
                                         NULL /* no dictionary */,
                                         TRUE);
}
#else
extern signed char v6ready;
#endif
