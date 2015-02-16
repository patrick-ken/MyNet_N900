#ifndef __PURE_FTPD_MEDIASERVER_H_
#define __PURE_FTPD_MEDIASERVER_H_

#define TYPE_ITUNE			1
#define TYPE_UPNPAV			2
#define TYPE_BOTH			3
#define ADD 				1
#define DEL 				2
//#define SCHEDULER_FILE		"/HD_a4/ScanMmsSch"
#define WIN32_DIR_DELIMITER_CHR	 '\\'
#define UNIX_DIR_DELIMITER_CHR	 '/'
//#define MEDIASERVER			"MediaServer"
#define MEDIASERVER			"UpdateDB"
#define ITUNE_FOLDER_FILE		"/etc/mt-daapd.conf"
#define UPNPAV_FOLDER_FILE		"/etc/upnpav.conf"
#define SHM_MOD			(SHM_R | SHM_W)
#define SHM_SIZE			1024
#define SHARE_FOLDER_MAX_LEN	256
#define MAX_LENGTH			1024

#define MY_PRINT(x)	

struct my_message
{
	long int message_type;
	char cmd[MAX_LENGTH];
};

enum MEDIABASETYPE {AUDIOTYPE,VIDEOTYPE,IMAGETYPE};
#define stricmp strcasecmp

enum MEDIABASETYPE GetMediaBaseType(char *strFileExt );
void MediaServer_Add_Del_File(const char *filename, int op_type, ino_t id);
void MediaServer_Rename_FIle(const char *from, const char *to, ino_t id);
void MediaServer_Rename_Dir(const char *from, const char *to);
char* GetFileExtension(char* pathName, int returnCopy, char* defaultRetval);
void UpdateScheduler(char *cmd_buf);
int CheckNASMediaFolder(const char *path);
int InitNASMediaFolder(void);
//int write_wlock( int fd );
//int clear_lock( int fd );
//void getPidOfSyncmms(int *pPid);
void writeDataToShm(void);
void readDataFromShm(void);
void getUsrPath(const char *usrname, char *usrPath);
#endif
