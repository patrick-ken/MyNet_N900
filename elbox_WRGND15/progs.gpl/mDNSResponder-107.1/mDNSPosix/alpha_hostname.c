#include <stdio.h>
#include <string.h>

int main(int argc, char **argv)
{
    char cmd[500];
    FILE *fp;

    sprintf(cmd,"hostname %s",PRODUCT_NAME);
    system(cmd);

    fp = fopen("/etc/hosts","w");
    if (fp==NULL)
	fprintf(stderr,"File /etc/hosts open error!\n");

    sprintf(cmd,"127.0.0.1 %s localhost.localdomain localhost\n",PRODUCT_NAME);
    fputs(cmd,fp);
    fclose(fp);

    return 1;
}
