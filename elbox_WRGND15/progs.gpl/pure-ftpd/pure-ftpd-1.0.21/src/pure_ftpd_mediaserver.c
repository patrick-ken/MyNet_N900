/*************************************************************************************************************************
 * Copyright(C) 2004-2008 ALPHA Networks Incorporation, All rights reserved.
 *    
 * This is unpublished proprietary source code of ALPHA, Incorp.
 *
 * The copyright notice above does not evidence any actual or intended
 * publication of such source code.
 **************************************************************************************************************************
 */

/*============================================================================
 * Module Name: pure_ftpd_mediaserver.c
 *
 * Module Function: The program will create a massage queue,and write command lines to the message queue,
 *		  then another process "getMsg" will read these command lines and append them to 
 *		  "/mnt/HD_a4/.systemfile/SacnMmsSch" after user uploaded, removed or renamed an audio/video 
 *		  file through pure-ftpd service.
 * Author Name: Space
 * Create Date: 22/10/2007
 *
 *=============================================================================
 */

/* History:
 * Date         	Name        	Comments
 *------------------------------------------------------------------------
 * 2007.10.22   	Space      	Created.
 * 2008.01.22   	Space      	Revised.
 */
#include <stdio.h>
#include <string.h>
#include <unistd.h>
#include <fcntl.h>
#include <errno.h>
#include	<signal.h>
#include "pure_ftpd_mediaserver.h"
#include <sys/ipc.h>		//+Space01222008
#include <sys/types.h>		//+Space01222008
#include <sys/shm.h>		//+Space01222008

char C_iTuneFolder[SHARE_FOLDER_MAX_LEN];		// child process's shared folder infomation
char C_UpnpavFolder[SHARE_FOLDER_MAX_LEN];
extern errno;
extern shmid;
extern char *shmptr;
extern char F_iTuneFolder[SHARE_FOLDER_MAX_LEN];		//+Space01222008  fathor process's shared folder infomation
extern char F_UpnpavFolder[SHARE_FOLDER_MAX_LEN];		//+Space01222008

void MediaServer_Add_Del_File(const char *filename, int op_type, ino_t id)
{
	char path[2048] = "";
	char *ext = NULL;
	char *cmd_buf = NULL;
	int path_type,cmd_buf_len;
	char op[8] = "";
	int which_folder = 0;

	if (id == -1)
	{
		MY_PRINT(printf("ino is -1\n");)
		return;
	}
	
	strncpy(path, filename, 2048);

	ext = (char*) GetFileExtension( path, 1,"");
	path_type = GetMediaBaseType( ext);
	if(!strlen(ext) ||path_type  == -1)
		return;

//	InitNASMediaFolder();
	readDataFromShm(); //+Space01222008
	which_folder = CheckNASMediaFolder(filename);
	if (which_folder == 0) 
		return;

	if (ADD == op_type)
	{
		cmd_buf_len = strlen(path)+30;
		cmd_buf = malloc(cmd_buf_len);

		if (which_folder == TYPE_BOTH && path_type == AUDIOTYPE)
			snprintf(cmd_buf,cmd_buf_len,"%s -C \"%s\"\n", MEDIASERVER, path);
		else if (which_folder == TYPE_BOTH)
			snprintf(cmd_buf,cmd_buf_len,"%s -C -M \"%s\"\n", MEDIASERVER, path);
		else if (which_folder == TYPE_UPNPAV)
			snprintf(cmd_buf,cmd_buf_len,"%s -C -M \"%s\"\n", MEDIASERVER, path);
		else if (which_folder == TYPE_ITUNE && path_type == AUDIOTYPE)
			snprintf(cmd_buf,cmd_buf_len,"%s -C -I \"%s\"\n", MEDIASERVER, path);
		else
			return;
		MY_PRINT(printf("SPACE------CMD_BUF<%s>\n", cmd_buf);)
		UpdateScheduler(cmd_buf);
		free(cmd_buf);
	}
	else if (DEL == op_type)
	{
		cmd_buf_len = strlen(path)+30;
		cmd_buf = malloc(cmd_buf_len);

		if (which_folder == TYPE_BOTH && path_type == AUDIOTYPE)
			snprintf(cmd_buf,cmd_buf_len,"%s -D \"%s\" %lu\n", MEDIASERVER, path, (unsigned long)id);
		else if (which_folder == TYPE_BOTH)
			snprintf(cmd_buf,cmd_buf_len,"%s -D -M \"%s\" %lu\n", MEDIASERVER, path, (unsigned long)id);
		else if (which_folder == TYPE_UPNPAV)
			snprintf(cmd_buf,cmd_buf_len,"%s -D -M \"%s\" %lu\n", MEDIASERVER, path, (unsigned long)id);
		else if (which_folder == TYPE_ITUNE && path_type == AUDIOTYPE)
			snprintf(cmd_buf,cmd_buf_len,"%s -D -I \"%s\" %lu\n", MEDIASERVER, path, (unsigned long)id);
		else
			return;
		MY_PRINT(printf("SPACE------CMD_BUF<%s>\n", cmd_buf);)
		UpdateScheduler(cmd_buf);
		free(cmd_buf);
	}

	//+Space01222008
	if (ext && ext[0])
		free(ext);
	//+Space01222008
}

void MediaServer_Rename_FIle(const char *from, const char *to, ino_t id)
{
	char path_from[2048] = "";
	char path_to[2048] = "";
	char *ext_from = NULL;
	char *ext_to = NULL;
	char *cmd_buf = NULL;
	int path_type_from, path_type_to, cmd_buf_len;
	int from_type,to_type; // type: ITUNE, UPNPAV or BOTH

	strncpy(path_from, from, 2048);
	strncpy(path_to, to, 2048);

	ext_from = (char*) GetFileExtension( path_from, 1,"");
	ext_to = (char*) GetFileExtension( path_to, 1,"");
	path_type_from = GetMediaBaseType( ext_from);
	path_type_to = GetMediaBaseType( ext_to);

	//Both names are not of media file type
	if((!strlen(ext_from) ||path_type_from == -1) && (!strlen(ext_to) ||path_type_to== -1))
	{
		if (ext_from && ext_from[0])
			free(ext_from);
		if (ext_to && ext_to[0])
			free(ext_to);
		
		return;
	}
	else if ((strlen(ext_from) && path_type_from != -1) && (!strlen(ext_to) ||path_type_to == -1))
	{// when rename_from name is of media file type
		MediaServer_Add_Del_File(from, DEL, id);
		
		if (ext_from && ext_from[0])
			free(ext_from);
		if (ext_to && ext_to[0])
			free(ext_to);
		
		return;
	}
	else if ((!strlen(ext_from) ||path_type_from == -1) && (strlen(ext_to) && path_type_to != -1))
	{// when rename_to name is of media file type
		MediaServer_Add_Del_File(to, ADD, 0);

		if (ext_from && ext_from[0])
			free(ext_from);
		if (ext_to && ext_to[0])
			free(ext_to);
		
		return;
	}

	if (path_type_from != path_type_to) 
	{
		MediaServer_Add_Del_File(from, DEL, id);
		MediaServer_Add_Del_File(to, ADD, 0);

		if (ext_from && ext_from[0])
			free(ext_from);
		if (ext_to && ext_to[0])
			free(ext_to);
		
		return;
	}

//	InitNASMediaFolder();
	readDataFromShm(); //+Space01222008
	from_type = CheckNASMediaFolder(path_from);
	to_type = CheckNASMediaFolder(path_to);

	if (!from_type && !to_type)
	{
		if (ext_from && ext_from[0])
			free(ext_from);
		if (ext_to && ext_to[0])
			free(ext_to);
		
		return;
	}
	
	cmd_buf_len = strlen(path_from) + strlen(path_to)  +30;
	cmd_buf = malloc(cmd_buf_len);
	if (! cmd_buf)
	{
		if (ext_from && ext_from[0])
			free(ext_from);
		if (ext_to && ext_to[0])
			free(ext_to);
		
		return;
	}

	if (path_type_to == AUDIOTYPE)
	{
		if (from_type == TYPE_BOTH)
		{
			if (to_type == TYPE_BOTH)
				snprintf(cmd_buf,cmd_buf_len,"%s -R \"%s\" \"%s\"\n", MEDIASERVER,  from, to);
			else if (to_type == TYPE_ITUNE)
			{
				snprintf(cmd_buf,cmd_buf_len,"%s -R -I \"%s\" \"%s\"\n", MEDIASERVER,  from, to);
				UpdateScheduler(cmd_buf);
				MY_PRINT(printf("SPACE------CMD_BUF<%s>\n", cmd_buf);)
				snprintf(cmd_buf,cmd_buf_len,"%s -D -M \"%s\" %lu\n", MEDIASERVER,  from, (unsigned long)id);
			}
			else if (to_type == TYPE_UPNPAV)
			{
				snprintf(cmd_buf,cmd_buf_len,"%s -R -M \"%s\" \"%s\"\n", MEDIASERVER,  from, to);
				UpdateScheduler(cmd_buf);
				MY_PRINT(printf("SPACE------CMD_BUF<%s>\n", cmd_buf);)
				snprintf(cmd_buf,cmd_buf_len,"%s -D -I \"%s\" %lu\n", MEDIASERVER,  from, (unsigned long)id);
			}
			else if (to_type == 0)
			{
				snprintf(cmd_buf,cmd_buf_len,"%s -D \"%s\" %lu\n", MEDIASERVER,  from, (unsigned long)id);
			}
		}
		else if (from_type == TYPE_ITUNE)
		{
			if (to_type == TYPE_ITUNE)
				snprintf(cmd_buf,cmd_buf_len,"%s -R -I \"%s\" \"%s\"\n", MEDIASERVER,  from, to);
			else if (to_type == TYPE_UPNPAV)
			{
				snprintf(cmd_buf,cmd_buf_len,"%s -D -I \"%s\" %lu\n", MEDIASERVER,  from, (unsigned long)id);
				UpdateScheduler(cmd_buf);
				MY_PRINT(printf("SPACE------CMD_BUF<%s>\n", cmd_buf);)
				snprintf(cmd_buf,cmd_buf_len,"%s -C -M \"%s\"\n", MEDIASERVER,  to);
			}
			else if (to_type == TYPE_BOTH)
			{
				snprintf(cmd_buf,cmd_buf_len,"%s -R -I \"%s\" \"%s\"\n", MEDIASERVER,  from, to);
				UpdateScheduler(cmd_buf);
				MY_PRINT(printf("SPACE------CMD_BUF<%s>\n", cmd_buf);)
				snprintf(cmd_buf,cmd_buf_len,"%s -C -M \"%s\"\n", MEDIASERVER,  to);
			}
			else if (to_type == 0)
			{
				snprintf(cmd_buf,cmd_buf_len,"%s -D -I \"%s\" %lu\n", MEDIASERVER,  from, (unsigned long)id);
			}
		}
		else if (from_type == TYPE_UPNPAV)
		{
			if (to_type == TYPE_UPNPAV)
				snprintf(cmd_buf,cmd_buf_len,"%s -R -M \"%s\" \"%s\"\n", MEDIASERVER,  from, to);
			else if (to_type == TYPE_ITUNE)
			{
				snprintf(cmd_buf,cmd_buf_len,"%s -D -M \"%s\" %lu\n", MEDIASERVER,  from, (unsigned long)id);
				UpdateScheduler(cmd_buf);
				MY_PRINT(printf("SPACE------CMD_BUF<%s>\n", cmd_buf);)
				snprintf(cmd_buf,cmd_buf_len,"%s -C -I \"%s\"\n", MEDIASERVER,  to);
			}
			else if (to_type == TYPE_BOTH)
			{
				snprintf(cmd_buf,cmd_buf_len,"%s -R -M \"%s\" \"%s\"\n", MEDIASERVER,  from, to);
				UpdateScheduler(cmd_buf);
				MY_PRINT(printf("SPACE------CMD_BUF<%s>\n", cmd_buf);)
				snprintf(cmd_buf,cmd_buf_len,"%s -C -I \"%s\"\n", MEDIASERVER,  to);
			}
			else if (to_type == 0)
			{
				snprintf(cmd_buf,cmd_buf_len,"%s -D -I \"%s\" %lu\n", MEDIASERVER,  from, (unsigned long)id);
			}
		}
		else if (from_type == 0)
		{
			if (to_type == TYPE_UPNPAV)
				snprintf(cmd_buf,cmd_buf_len,"%s -C -M \"%s\" \"%s\"\n", MEDIASERVER, to);
			else if (to_type == TYPE_ITUNE)
				snprintf(cmd_buf,cmd_buf_len,"%s -C -I \"%s\"\n", MEDIASERVER,  to);
			else if (to_type == TYPE_BOTH)
				snprintf(cmd_buf,cmd_buf_len,"%s -C \"%s\"\n", MEDIASERVER,  to);
		}	
	}
	else if (path_type_to == VIDEOTYPE)
	{
		if ((from_type == TYPE_BOTH ||from_type == TYPE_UPNPAV) && (to_type == TYPE_BOTH || to_type == TYPE_UPNPAV))
			snprintf(cmd_buf,cmd_buf_len,"%s -R -M \"%s\" \"%s\"\n", MEDIASERVER,  from, to);
		else if (to_type == TYPE_BOTH || to_type == TYPE_UPNPAV)
			snprintf(cmd_buf,cmd_buf_len,"%s -C -M \"%s\"\n", MEDIASERVER,  to);
		else if ((from_type == TYPE_BOTH ||from_type == TYPE_UPNPAV) && (to_type == TYPE_ITUNE || to_type == 0)) 
			snprintf(cmd_buf,cmd_buf_len,"%s -D -M \"%s\" %lu\n", MEDIASERVER,  from, (unsigned long)id);
	}
	else if (path_type_to == IMAGETYPE)
	{
		if ((from_type == TYPE_BOTH ||from_type == TYPE_UPNPAV) && (to_type == TYPE_BOTH || to_type == TYPE_UPNPAV)) 
			snprintf(cmd_buf,cmd_buf_len,"%s -R -M \"%s\" \"%s\"\n", MEDIASERVER,  from, to);
		else if (to_type == TYPE_BOTH || to_type == TYPE_UPNPAV)
			snprintf(cmd_buf,cmd_buf_len,"%s -C -M \"%s\"\n", MEDIASERVER,  to);
		else if ((from_type == TYPE_BOTH ||from_type == TYPE_UPNPAV) && (to_type == TYPE_ITUNE || to_type == 0)) 
			snprintf(cmd_buf,cmd_buf_len,"%s -D -M \"%s\" %lu\n", MEDIASERVER,  from, (unsigned long)id);
	}
	else
	{
		if (ext_from && ext_from[0])
			free(ext_from);
		if (ext_to && ext_to[0])
			free(ext_to);
		
		return;
	}
	
	MY_PRINT(printf("SPACE------CMD_BUF<%s>\n", cmd_buf);)
	UpdateScheduler(cmd_buf);
	free(cmd_buf);

	if (ext_from && ext_from[0])
		free(ext_from);
	if (ext_to && ext_to[0])
		free(ext_to);
}

void MediaServer_Rename_Dir(const char *from, const char *to)
{
	int from_type,to_type; // type: ITUNE, UPNPAV or BOTH
	char *cmd_buf = NULL;
	int cmd_buf_len = 0;

//	InitNASMediaFolder();
	readDataFromShm(); //+Space01222008
	from_type = CheckNASMediaFolder(from);
	to_type = CheckNASMediaFolder(to);

	if (!from_type && !to_type)
		return;

	cmd_buf_len = strlen(from) + strlen(to)  +30;
	cmd_buf = malloc(cmd_buf_len);
	if (! cmd_buf)
		return;
	
	if (from_type == 0)
	{
		if (to_type == TYPE_BOTH)
			snprintf(cmd_buf,cmd_buf_len,"%s -C \"%s\"\n", MEDIASERVER,  to);
		else if (to_type == TYPE_ITUNE)
			snprintf(cmd_buf,cmd_buf_len,"%s -C -I \"%s\"\n", MEDIASERVER,  to);
		else if (to_type == TYPE_UPNPAV)
			snprintf(cmd_buf,cmd_buf_len,"%s -C -M \"%s\"\n", MEDIASERVER,  to);
	}
	else if (from_type == TYPE_BOTH)
	{
		if (to_type == TYPE_BOTH)
			snprintf(cmd_buf,cmd_buf_len,"%s -R \"%s\" \"%s\"\n", MEDIASERVER,  from, to);
		else if (to_type == TYPE_ITUNE)
		{
			snprintf(cmd_buf,cmd_buf_len,"%s -R -I \"%s\" \"%s\"\n", MEDIASERVER,  from, to);
			UpdateScheduler(cmd_buf);
			MY_PRINT(printf("SPACE------CMD_BUF<%s>\n", cmd_buf);)
			snprintf(cmd_buf,cmd_buf_len,"%s -DD -M \"%s\"\n", MEDIASERVER,  from);
		}
		else if (to_type == TYPE_UPNPAV)
		{
			snprintf(cmd_buf,cmd_buf_len,"%s -R -M \"%s\" \"%s\"\n", MEDIASERVER,  from, to);
			UpdateScheduler(cmd_buf);
			MY_PRINT(printf("SPACE------CMD_BUF<%s>\n", cmd_buf);)
			snprintf(cmd_buf,cmd_buf_len,"%s -DD -I \"%s\"\n", MEDIASERVER,  from);
		}
		else if (to_type == 0)
			snprintf(cmd_buf,cmd_buf_len,"%s -DD \"%s\"\n", MEDIASERVER,  from);
	}
	else if (from_type == TYPE_ITUNE)
	{
		if (to_type == TYPE_BOTH)
		{
			snprintf(cmd_buf,cmd_buf_len,"%s -R -I \"%s\" \"%s\"\n", MEDIASERVER,  from, to);
			UpdateScheduler(cmd_buf);
			MY_PRINT(printf("SPACE------CMD_BUF<%s>\n", cmd_buf);)
			snprintf(cmd_buf,cmd_buf_len,"%s -C -M \"%s\"\n", MEDIASERVER,  to);
		}
		else if (to_type == TYPE_ITUNE)
		{
			snprintf(cmd_buf,cmd_buf_len,"%s -R -I \"%s\" \"%s\"\n", MEDIASERVER,  from, to);
		}
		else if (to_type == TYPE_UPNPAV)
		{
			snprintf(cmd_buf,cmd_buf_len,"%s -DD -I \"%s\"\n", MEDIASERVER,  from);
			UpdateScheduler(cmd_buf);
			MY_PRINT(printf("SPACE------CMD_BUF<%s>\n", cmd_buf);)
			snprintf(cmd_buf,cmd_buf_len,"%s -C -M \"%s\"\n", MEDIASERVER,  to);
		}
		else if (to_type == 0)
			snprintf(cmd_buf,cmd_buf_len,"%s -DD -I \"%s\"\n", MEDIASERVER,  from);
	}
	else if (from_type == TYPE_UPNPAV)
	{
		if (to_type == TYPE_BOTH)
		{
			snprintf(cmd_buf,cmd_buf_len,"%s -R -M \"%s\" \"%s\"\n", MEDIASERVER,  from, to);
			UpdateScheduler(cmd_buf);
			MY_PRINT(printf("SPACE------CMD_BUF<%s>\n", cmd_buf);)
			snprintf(cmd_buf,cmd_buf_len,"%s -C -I \"%s\"\n", MEDIASERVER,  to);
		}
		else if (to_type == TYPE_UPNPAV)
		{
			snprintf(cmd_buf,cmd_buf_len,"%s -R -M \"%s\" \"%s\"\n", MEDIASERVER,  from, to);
		}
		else if (to_type == TYPE_ITUNE)
		{
			snprintf(cmd_buf,cmd_buf_len,"%s -DD -M \"%s\"\n", MEDIASERVER,  from);
			UpdateScheduler(cmd_buf);
			MY_PRINT(printf("SPACE------CMD_BUF<%s>\n", cmd_buf);)
			snprintf(cmd_buf,cmd_buf_len,"%s -C -I \"%s\"\n", MEDIASERVER,  to);
		}
		else if (to_type == 0)
			snprintf(cmd_buf,cmd_buf_len,"%s -DD -M \"%s\"\n", MEDIASERVER,  from);
	}
	else 
		return;

	MY_PRINT(printf("SPACE------CMD_BUF<%s>\n", cmd_buf);)
	UpdateScheduler(cmd_buf);
	free(cmd_buf);
}

/* 
* Returns file extension for ASCII-encoded paths. 
* return NULL : error; defaultRetval or a ext : correct.
*/
char* GetFileExtension(char* pathName, int returnCopy, char* defaultRetval)
{
	int len;
	int i;

	if(NULL == defaultRetval)
	{
		return NULL;
	}

	if(NULL == pathName)
	{
		return defaultRetval;
	}
	len = (int) strlen(pathName);
	
	for (i = len-1; i >= 0; i--)
	{
		if (
			(WIN32_DIR_DELIMITER_CHR == pathName[i]) ||
			(UNIX_DIR_DELIMITER_CHR == pathName[i])
			)
		{
			return defaultRetval;
		}
		
		if ('.' == pathName[i])
		{
			if (returnCopy == 0)
			{
				return pathName+i;
			}
			else
			{
				char* deepCopy = (char*) malloc(len+1);

				strcpy(deepCopy, pathName+i);
				return deepCopy;
			}
		}
	}
	
	return defaultRetval;
}

enum MEDIABASETYPE GetMediaBaseType(char *strFileExt )
{
	if(
		stricmp(".mp3",strFileExt) == 0 ||
		stricmp(".wav",strFileExt) == 0 ||
		stricmp(".lpcm",strFileExt) == 0 ||
		stricmp(".pcm",strFileExt) == 0 ||
		stricmp(".m4p",strFileExt) == 0 ||
		stricmp(".m4a",strFileExt) == 0 ||
		stricmp(".wma",strFileExt) == 0 ||
		stricmp(".mp2",strFileExt) == 0 ||
		stricmp(".mp1",strFileExt) == 0 ||
		stricmp(".mpa",strFileExt) == 0 ||
		stricmp(".aif",strFileExt) == 0 ||
   		stricmp(".aiff",strFileExt) == 0 ||
        stricmp(".m3u",strFileExt) == 0 ||
        stricmp(".pls",strFileExt) == 0 ||
        stricmp(".ogg",strFileExt) == 0 ||
       	stricmp(".aac",strFileExt) == 0 
	)
		return AUDIOTYPE;

    if(
       	stricmp(".mpg",strFileExt) == 0 ||
        stricmp(".mpeg",strFileExt) == 0 ||
        stricmp(".dat",strFileExt) == 0 ||
        stricmp(".mpeg2",strFileExt) == 0 ||
        stricmp(".mp4",strFileExt) == 0 ||
        stricmp(".avi",strFileExt) == 0 ||
        stricmp(".vob",strFileExt) == 0 ||
        stricmp(".wmv",strFileExt) == 0 ||
        stricmp(".mov",strFileExt) == 0 
	)
		return VIDEOTYPE;		

    if(
    	stricmp(".jpg",strFileExt) == 0 ||
        stricmp(".jpeg",strFileExt) == 0 ||
        stricmp(".tif",strFileExt) == 0 ||
        stricmp(".tiff",strFileExt) == 0 ||
        stricmp(".png",strFileExt) == 0 ||
        stricmp(".gif",strFileExt) == 0 ||
        stricmp(".bmp",strFileExt) == 0 
	)
		return IMAGETYPE;
	else
		return -1;

}

/* //mark by Space01222008
void UpdateScheduler(char *cmd_buf)
{
	int fdsch;
	int pid = -1;

	fdsch = open( SCHEDULER_FILE, O_APPEND|O_WRONLY);
	MY_PRINT(printf("SPACE------OPEN FILE<%s> FD<%d>\n", SCHEDULER_FILE, fdsch);)
	if (fdsch == -1)
	{
		MY_PRINT(printf("SPACE------OPEN FILE FAIL!\n");)
		MY_PRINT(printf("%s\n", strerror(errno));)
		return;
	}
	
	write_wlock(fdsch);
	write(fdsch, cmd_buf, strlen(cmd_buf) );
	clear_lock(fdsch);
	close(fdsch);

	//become_root();
	getPidOfSyncmms(&pid);
	kill((pid_t)pid, SIGUSR1);
	//unbecome_root();
}
*/
void UpdateScheduler(char *cmd_buf)
{
	struct my_message send_msg;
	int msgid;
	
	msgid = msgget((key_t)9527,  0666|IPC_CREAT);
	if ( msgid == -1 )
	{
		MY_PRINT(printf("UpdateScheduler: msgget() error!\n");)
		return;
	}
	
	send_msg.message_type = 6;
	strncpy(send_msg.cmd, cmd_buf, MAX_LENGTH);
	
	msgsnd(msgid, (void *)&send_msg, sizeof(send_msg.cmd), 0);
}

/* //mark by Space01222008
int write_wlock( int fd )
{
	struct flock	stFLock;
	
	stFLock.l_type = F_WRLCK;
	stFLock.l_start = 0;
	stFLock.l_whence = SEEK_END;
	stFLock.l_len = 0;

	if ( -1 == fcntl( fd, F_SETLKW, &stFLock)  )
	{
		MY_PRINT(printf("FILE:%s, LINE:%s   Lock File Error.\n", __FILE__, __LINE__ );)
		return -1;
	}

	
	return 0;
}

int clear_lock( int fd )
{
	struct flock	stFLock;
	
	stFLock.l_type = F_UNLCK;
	stFLock.l_start = 0;
	stFLock.l_whence = SEEK_SET;
	stFLock.l_len = 0;

	if( -1 == fcntl( fd, F_SETLKW, &stFLock ) )
	{
		MY_PRINT(printf("UNLock File Error!File:%s Line:%s\n",__FILE__,__LINE__);)
		return -1;
	}
	
	return 0;
}
*/

int getID(char *path, ino_t *id)
{
	struct stat buf;

	MY_PRINT(printf("SPACE------GET ID PATH<%s>\n", path);)
	if (stat(path, &buf) == -1)
	{
		MY_PRINT(printf("SPACE------GET ID STAT ERR\n");)
		return -1;
	}

	*id = buf.st_ino;
	return 0;
}

int InitNASMediaFolder(void)
{
	FILE *file;
	char temp[256] = "";
	int get_itune_folder = 0;
	char *p = NULL;
	char *q = NULL;

	memset(F_iTuneFolder, 0, SHARE_FOLDER_MAX_LEN);
	memset(F_iTuneFolder, 0, SHARE_FOLDER_MAX_LEN);

	//read itune folder info
	if (access(ITUNE_FOLDER_FILE,R_OK) == 0)
	{
		file = fopen(ITUNE_FOLDER_FILE,"r");
		if (file == NULL)
		{
			MY_PRINT(printf("InitNASMediaFolder: open file %s fails\n",ITUNE_FOLDER_FILE);)
			F_iTuneFolder[0] = '\0';
			return -1;
		}
	
		while (fgets(temp,256,file) != NULL)
		{
			if (strstr(temp, "mp3_dir"))
			{
				p = strchr(temp, '/');
				if (p)
				{
					strcpy(F_iTuneFolder, p);
					if ((q = strchr(F_iTuneFolder, '\n')) != NULL)
						*q = '\0';
					get_itune_folder = 1;
				}
				break;
			}
		}

		fclose(file);
		if (get_itune_folder == 0)
		{
			MY_PRINT(printf("InitNASMediaFolder: get itune shared folder fails\n");)
			F_iTuneFolder[0] = '\0';
		}
	}
	else
		F_iTuneFolder[0] = '\0';	
	
	//read upnpav folder info
	if (access(UPNPAV_FOLDER_FILE,R_OK) == 0)
	{
		file = fopen(UPNPAV_FOLDER_FILE,"r");
		if (file == NULL)
		{
			MY_PRINT(printf("InitNASMediaFolder: open file %s fails\n",UPNPAV_FOLDER_FILE);)
			F_UpnpavFolder[0] = '\0';
			return -1;
		}

		if (fgets(temp,256,file) == NULL)
		{
			MY_PRINT(printf("InitNASMediaFolder: read file %s fails\n",UPNPAV_FOLDER_FILE);)
			F_UpnpavFolder[0] = '\0';
		}

		fclose(file);
		p = strchr(temp, '/');
		if (p)
		{
			strcpy(F_UpnpavFolder, p);
		 	if ((q = strchr(F_UpnpavFolder, '\n')) != NULL)
				*q = '\0';
		}
		else
		{
			MY_PRINT(printf("InitNASMediaFolder: get upnpav shared folder fails\n");)
			F_UpnpavFolder[0] = '\0';
		}
			
	}
	else
		F_UpnpavFolder[0] = '\0';

	MY_PRINT(printf("InitNASMediaFolder: UpnpavFolder = [%s]\n",F_UpnpavFolder);)
	MY_PRINT(printf("InitNASMediaFolder: iTuneFolder = [%s]\n",F_iTuneFolder);)
	
	return 0;
}

/*--------------------------------------------------------------------------------
 *	check whether "path" is included in share folders of itune/upnpav server
 *	return value:			
 *	0 	-- 	"path" is  NOT included in share folders of itune/upnpav server
 *	else 	--	"path" is included in share folders of itune/upnpav server
 *---------------------------------------------------------------------------------*/
int CheckNASMediaFolder(const char *path)
{
	int len = 0;
	int flag_itune = 0;
	int flag_upnpav = 0;

	len = strlen(C_iTuneFolder);
	if (len && !strncmp(path,C_iTuneFolder,len)
	          && (*(path+len) == '\0' ||*(path+len) == '/')) //+Space01222008
		flag_itune = 1;

	len = strlen(C_UpnpavFolder);
	if (len && !strncmp(path,C_UpnpavFolder,len)
	          && (*(path+len) == '\0' ||*(path+len) == '/')) //+Space01222008
		flag_upnpav = 1;

	if (flag_itune && flag_upnpav)
		return TYPE_BOTH;

	if (flag_itune)
		return TYPE_ITUNE;
	else if (flag_upnpav)
		return TYPE_UPNPAV;
	else 
		return 0;	
}

/* //mark by Space01222008
void getPidOfSyncmms(int *pPid)
{
	FILE *fp = NULL;
	char pid[16] = "";

	if ((fp = fopen("/HD_a4/pidSync", "r")) == NULL)
	{
		MY_PRINT(printf("Open file /HD_a4/pidSync fail!\n");)
		return;
	}

	if (fgets(pid, 15, fp) == NULL)
	{
		MY_PRINT(printf("Read fail!\n");)
		fclose(fp);
		return;
	}

	if (pid[strlen(pid) -1] == '\n')
		pid[strlen(pid) -1] = 0x00;
	*pPid = atoi(pid);
	fclose(fp);
	MY_PRINT(printf("Pid = %d\n", *pPid);)
}
*/

void writeDataToShm(void)
{
	if (shmid == -1) //Initialize, "shmid" was initialized to be -1 before
	{
		if ((shmid = shmget(IPC_PRIVATE, SHM_SIZE, SHM_MOD)) <  0) 
		{
			MY_PRINT(printf("Get shm error!\n");)
			return;
		}
		
		if ((shmptr = shmat(shmid, 0, 0)) == (void *)-1)
		{
			MY_PRINT(printf("Get shared mem pointer error!\n");)
			return;
		}
	}
	else
	{
		shmctl(shmid, SHM_LOCK, 0);
		memset(shmptr, 0, SHM_SIZE);
	}

	strncpy(shmptr, F_iTuneFolder, SHM_SIZE);
	strcat(shmptr, ":");
	strcat(shmptr, F_UpnpavFolder);

	shmctl(shmid, SHM_UNLOCK, 0);
	MY_PRINT(printf("shmid<%d>, shmptr<%s>\n", shmid, shmptr);)
}

void readDataFromShm(void)
{
	char *pbegin = NULL;
	char *pend = NULL;

	MY_PRINT(printf("shmid<%d>, shmptr<%s>\n", shmid, shmptr);)
	memset(C_iTuneFolder, 0, SHARE_FOLDER_MAX_LEN);
	memset(C_UpnpavFolder, 0, SHARE_FOLDER_MAX_LEN);
	
	pbegin = shmptr;
	pend = strchr(shmptr, ':');
	strncpy(C_iTuneFolder, pbegin, pend - pbegin);

	pbegin = pend + 1;
	pend = strchr(pbegin, '\0');
	strncpy(C_UpnpavFolder, pbegin, pend - pbegin);

	MY_PRINT(printf("readDataFromShm: UpnpavFolder = [%s]\n",C_UpnpavFolder);)
	MY_PRINT(printf("readDataFromShm: iTuneFolder = [%s]\n",C_iTuneFolder);)
}

void getUsrPath(const char *usrname, char *usrPath)
{
	FILE *fp = NULL;
	char line[128] = "";
	char *p_begin = NULL;
	char *p_end = NULL;

	if ((fp = fopen("/etc/passwd", "r")) == NULL)
	{
		MY_PRINT(printf("Open file /etc/passwd fail!\n");)
		return;
	}

	while (fgets(line, 128, fp) != NULL)
	{
		if (strstr(line, usrname))
		{
			p_begin = strchr(line, '/');
			if (p_begin)
				p_end = strchr(p_begin, ':');
			else
				break;
			
			if (p_end)
			{
				strncpy(usrPath, p_begin, p_end - p_begin);
				MY_PRINT(printf("SPACE------getUsrPath: usrPath = [%s]\n", usrPath);)
				break;
			}
			else
				break;
		}
	}

	fclose(fp);
}