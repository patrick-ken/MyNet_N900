#include <stdio.h>
#include <string.h>
#include <ctype.h>
#include <stdlib.h>
#include <sys/types.h>

//#define DBG

int Transfer_Path(char *read_path, char *full_path, int rootflag)
{
	char *p1;
	int id = 0;
	memset(full_path, 0x00, sizeof(full_path));

	p1 = strchr(read_path, '_');
	*p1 = 0x00;
	p1++;
	id = atoi(p1);
	p1--;
	*p1 = '_';
#ifdef DBG
	printf("id = [%d]\n", id);
#endif

	if ((p1 = strchr(read_path, '/')) != NULL)	
	{
		*p1 = 0x00;
		p1++;
		if (id <= 100)
		{
			if (rootflag == 1)
				sprintf(full_path, "/mnt/vg%d/lvnas_default/", id);
			else
				sprintf(full_path, "/mnt/vg%d/lvnas_default/%s", id, p1);
		}
		else
		{
			if (rootflag == 1)
				sprintf(full_path, "/mnt/md%d/", id);
			else
				sprintf(full_path, "/mnt/md%s/%d", id, p1);
		}
		p1--;
		*p1 = '/';
	}
	else
	{
		if (id <= 100)
			sprintf(full_path, "/mnt/vg%d/lvnas_default", id);
		else
			sprintf(full_path, "/mnt/md%d", id);
	}
	return 0;
}
