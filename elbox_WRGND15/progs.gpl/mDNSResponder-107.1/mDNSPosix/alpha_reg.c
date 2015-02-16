#include <sys/types.h>
#include <sys/ipc.h>
#include <sys/sem.h>
#include <errno.h>
#include <unistd.h>
#include <sys/stat.h>
#include <stdlib.h>
#include <stdio.h>
#include <string.h>
#ifdef ALPHA_SHM
// for share memory
//#include "board.h"
#include <sys/shm.h>
#endif

#define BONJOUR_SEM_KEY  0x55490001
//#define BONJOUR_SEM_NUM  1
#define FTP_TYPE	0
#define SMB_TYPE	1
#define HTTP_TYPE	2  //personal web
#define PRINTER_TYPE	3  //lpd
#define WEBCAM_TYPE	4

#define	BONJOUR_CONF_FN	"/var/Bonjour.txt"
#define	BONJOUR_REG_FN	"/var/tmp/alpha_bonjour.txt"
#define	BONJOUR_PID_FN	"/var/run/mDNSResponder.pid"

union semun {
        int val;
        struct semid_ds *buf;
        unsigned short int *array;
        struct seminfo *__buf;
};

#ifdef ALPHA_SHM
static romeCfgParam_t *pRomeCfgParam;
void Alpha_Get_Rome_Ptr(void)
{
        int shmid=0;
        shmid=shmget(SHM_PROMECFGPARAM,sizeof(romeCfgParam_t),0666|IPC_CREAT);
        pRomeCfgParam=shmat(shmid,(void*)0,0);
}
#endif

int alpha_sem_get(key_t key)
{
	int sem_id;
        //First, try to create the semaphore!
        if ( (sem_id = semget(key, 1, 0666 | IPC_CREAT | IPC_EXCL))  >= 0)  
	{  //Create semaphore success!
                //Initialize the semahpore.
                union semun argument;
                unsigned short values = 1;
                argument.array = &values;
                semctl(sem_id, 0 , SETALL, argument);  //reset the sem ds.
        } 
	else 
	{  //Create semahpore failed! May it's already created! we try to get the sem id!
                if ( (sem_id = semget(key, 0, 0666)) == -1)  //Get sem id failed! we just exit and do nothing!
                {
                        fprintf(stderr, "create semaphore failed! and semget error! err=%d! msg = %s!\n", errno, strerror(errno));
                }
        }
	return sem_id;
}

void init_semaphore_struct(struct sembuf *sem, int sem_num, int sem_op, int sem_flg)
{
        sem->sem_num = sem_num; //Use the first semahpore.
        sem->sem_op = sem_op;           //Decrement by 1
        sem->sem_flg = sem_flg;                 //Permit undoing.
}

int alpha_sema_lock(int sem_id,int index)
{
        struct sembuf sbuffer;
        int err = -1;

        init_semaphore_struct(&sbuffer, index , -1, SEM_UNDO);

        while( ((err = semop(sem_id, &sbuffer, 1)) == -1) && (errno == EINTR));

        if (err < 0)
                fprintf(stderr, "semop failed! err=%d, msg = %s!\n", errno, strerror(errno));

        return err;

}

int alpha_sema_unlock(int sem_id, int index)
{
        struct sembuf sbuffer;
        int err = -1;

        init_semaphore_struct(&sbuffer, index, 1, SEM_UNDO);

        while( ((err = semop(sem_id, &sbuffer, 1)) == -1) && (errno == EINTR));
        if (err < 0)
                fprintf(stderr, "semop failed! err=%d, msg = %s!\n", errno, strerror(errno));

        return err;
}

int bonjour_dereg_service(int type, char *name, int port)
{
	FILE *fp;
	char str[200],line[200],tmpStr[4000],cmd[100];

//printf("bonjour_dereg_service\n");

	memset(tmpStr, 0, 4000);

	sprintf(cmd,"touch %s",BONJOUR_REG_FN);
	system(cmd);
	fp = fopen(BONJOUR_REG_FN,"r");
	if (fp == NULL)
	{
	  fprintf(stderr,"FILE %s open error!\n",BONJOUR_REG_FN);
	  return 0;
	}

	sprintf(str,"%d\t[%s]\t", type, name);
	tmpStr[0]='\0';
	// if the service exist, do nothing.
	while(fgets(line,200,fp))
	{
	  if(!strstr(line,str))
	    strcat(tmpStr,line);
	}
	fclose(fp);

	fp = fopen(BONJOUR_REG_FN,"w");
	if (fp == NULL)
	{
	  fprintf(stderr,"FILE %s open error!\n",BONJOUR_REG_FN);
	  return 0;
	}

	if(tmpStr!=NULL)
	  fprintf(fp,"%s",tmpStr);

	fclose(fp);

	return 1;
}

int bonjour_reg_service(int type, char *name, int port, char *queue)
{
	FILE *fp;
	char str[200],line[200],cmd[100];
	int exist=0;

//printf("bonjour_reg_service\n");

	memset(str,0,200);

	sprintf(cmd,"touch %s",BONJOUR_REG_FN);
	system(cmd);
	fp = fopen(BONJOUR_REG_FN,"r+");
	if (fp == NULL)
	{
	  fprintf(stderr,"FILE %s open error!\n",BONJOUR_REG_FN);
	  return 0;
	}

	if (type==3) //printer
	  sprintf(str,"%d\t[%s]\t%d\t[%s]\n", type, name, port, queue);
	else
	  sprintf(str,"%d\t[%s]\t%d\n", type, name, port);
	// if the service exist, do nothing.
	while(fgets(line,200,fp))
	{
	  if(strstr(line,str))
	  {
	    exist=1;
	    break;
	  }
	}
	
	if(!exist)
	{
	  fseek(fp,0,SEEK_END);
	  fputs(str,fp);
	}


	fclose(fp);

	return (1-exist);
}

void restartBonjour()
{
	FILE *fp,*fp2;
	char line[200],tmpStr[200];
	char name[100],queue[100];
	int type,port;
	int count=0;
	int b_pid;
	char *service[5] = {"_ftp._tcp","_smb._tcp","_http._tcp","_printer._tcp","_ica-networking._tcp"};

	fp = fopen(BONJOUR_REG_FN,"r");
	fp2 = fopen(BONJOUR_CONF_FN,"w");
	if (fp == NULL || fp2==NULL)
	{
	  fprintf(stderr,"FILE %s or %s open error!\n",BONJOUR_REG_FN,BONJOUR_CONF_FN);
	  fprintf(stderr,"So restartBonjour aborted!\n");
	  return;
	}

	// write Bonjour.txt file
	while(fgets(line,200,fp))
	{
//fprintf(stderr,"line=%s\n",line);
	  sscanf(line,"%d\t[%[^]]]\t%d\t[%[^]]]", &type, name, &port, queue);
//fprintf(stderr,"name=%s,queue=%s\n",name,queue);
	  fprintf(fp2,"\n");
	  fprintf(fp2,"%s\n",name);
	  fprintf(fp2,"%s local.\n",service[type]);
	  fprintf(fp2,"%d\n",port);
	  if (type==3)
	  {
//	    fprintf(fp2,"rp=%s\n",name);
	    fprintf(fp2,"rp=%s\n",queue);
	    fprintf(fp2,"qtotal=2\n",port);
	  }
	  count++;
	}

	fclose(fp);
	fclose(fp2);

	// stop bonjour
	fp=fopen(BONJOUR_PID_FN,"r");
	if (fp)
	{
	  fscanf(fp,"%d",&b_pid);
	  sprintf(tmpStr,"kill -2 %d",b_pid); // SIGINT
	  fprintf(stderr,"Stanley:%s\n",tmpStr);
	  system(tmpStr);
	  fclose(fp);
	  unlink(BONJOUR_PID_FN);
	}

#ifdef ALPHA_SHM
#if 0  //always on Now.
	if(pRomeCfgParam->alphaRomeCfgParam.bonjourCfgParam.enable==0)//disable
          return;
#endif
#endif

	// start bonjour
	if(count!=0) // if empty service, we don't start bonjour
	{
	  int pid;
	  //sprintf(tmpStr,"/bin/mDNSResponderPosix -f %s -v 2 &",BONJOUR_CONF_FN);
	  //system(tmpStr);
	  if( (pid = fork()) < 0) //error
	  {
	
	  } 
	  else if (pid == 0) //child
	  {
		execlp("/bin/mDNSResponderPosix", "mDNSResponderPosix", "-f", BONJOUR_CONF_FN, "-v", "2", NULL);
	 	exit(1);
	  } else { //parent
                FILE *fp;

                fp = fopen(BONJOUR_PID_FN, "w");
                if (fp != NULL) {
                    fprintf(fp, "%ld\n", pid);
                    fclose(fp);
                }

	  }
	}

	return;
}

int main(int argc, char **argv)
{
        int err=-1;
        int sem_id=-1;
	int reg=1,type=-1,port=-1;
	char rc,*name=NULL,*queue=NULL;

        while ((rc = getopt(argc, argv, "a:t:n:p:q:h?")) > 0) 
	{
                switch (rc) {
                case 'a':
			 if(!strcasecmp(optarg,"dereg"))
			   reg=0;
                         break;
                case 't':
			 type=atoi(optarg);
                         break;
                case 'n':
			 name=optarg;
                         break;
                case 'p':
			 port=atoi(optarg);
                         break;
                case 'q':
			 queue=optarg;
                         break;
                case 'h':
                case '?':
                default:
                        fprintf(stderr,"usage: alpha_rg -a <reg>[|<dereg>] -t type -n name -p port [-q queue]\n");
                        fprintf(stderr,"\ttype 0\t\"FTP Service\"\n");
                        fprintf(stderr,"\ttype 1\t\"SMB Service\"\n");
                        fprintf(stderr,"\ttype 2\t\"HTTP Service\"\n");
                        fprintf(stderr,"\ttype 3\t\"Printer Service\"\n");
                        fprintf(stderr,"\ttype 4\t\"WebCam Service\"\n");
			goto quit;
                        break;
                }
        }

	if(type==-1 || (reg==1 && port==-1) || name==NULL || (reg==1 && type==3 && queue==NULL))
	{
	    fprintf(stderr,"alpha_reg argument error!\n");
            fprintf(stderr,"usage: alpha_rg -a <reg>[|<dereg>] -t type -n name -p port [-q queue]\n");
            fprintf(stderr,"\ttype 0\t\"FTP Service\"\n");
            fprintf(stderr,"\ttype 1\t\"SMB Service\"\n");
            fprintf(stderr,"\ttype 2\t\"HTTP Service\"\n");
            fprintf(stderr,"\ttype 3\t\"Printer Service\"\n");
            fprintf(stderr,"\ttype 4\t\"WebCam Service\"\n");
	    goto quit;
	}

#ifdef ALPHA_SHM
        //share memory
        Alpha_Get_Rome_Ptr();
#endif


        //Now try to get the semaphore!
	if((sem_id = alpha_sem_get(BONJOUR_SEM_KEY))<0)
	    goto quit;

        if (alpha_sema_lock(sem_id, 0) <0)
            goto quit;

	if(reg)
	  err = bonjour_reg_service(type, name, port, queue);
	else
	  err = bonjour_dereg_service(type, name, port);

	restartBonjour();
/*
	{
	  struct stat my_stat;
	  while(stat(BONJOUR_PID_FN, &my_stat)==-1)
	    sleep(1);
	}
*/

        //Release the semaphore
        alpha_sema_unlock(sem_id, 0);

quit:
	exit(0);
        return err;
}

