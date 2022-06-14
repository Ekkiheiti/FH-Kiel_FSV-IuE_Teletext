#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include <unistd.h>
#include <signal.h>
#include <time.h>

void draw() {
	fflush(stdout);
	usleep(32000);
}

#define clear() printf("\e[1;1H\e[2J")
#define move(y, x) printf("\e[%d;%dH", y, x)
#define fore(r,g,b) printf("\e[38;2;%d;%d;%dm", r,g,b)
#define back(r,g,b) printf("\e[48;2;%d;%d;%dm", r,g,b)
#define WHITE 255, 255, 255
#define BLACK 0, 0, 0

void ptime() {
	move(0, 68);
	time_t ts = time(NULL);
	struct tm* tm = localtime(&ts);
	char s[6];
	strftime(s, 6, "%H:%M", tm);
	fore(255, 255, 255);
	back(0, 0, 0);
	printf("%s", s);
	draw();
}

int myloadpage(int num, int cls) {
	if(num < 100 || num > 999) num = 100;
	char fn[14];
	sprintf(fn, "pages/%03d.tele", num);
	FILE* fd = fopen(fn, "r");
	if(!fd) return 0;

	if(cls) {
		fore(255, 255, 255);
		back(0, 0, 0);
		clear();
		move(0, 0);
		printf("Teletext	zum Hauptmenü:    Taste  <<  | Seite 100");
		draw();
		move(0, 76);
		printf("%03d", num);
		draw();
	}
	ptime();

	move(2, 0);
	char buf[17];
	size_t len;
	while((len = fread(buf, 1, 16, fd)) > 0) {
		buf[len] = 0;
		fwrite(buf, 1, len, stdout);
		draw();
	}
	fclose(fd);
	return 1;
}

int num = 100;
void sig(int code);

int loadpage(int num, int cls) {
	signal(SIGUSR1, SIG_IGN);
	signal(SIGUSR2, SIG_IGN);
	int res = myloadpage(num, cls);
	signal(SIGUSR1, sig);
	signal(SIGUSR2, sig);
	return res;
}

void xload() {
	FILE* fd = fopen("tele.load", "r");
	fscanf(fd, "%d", &num);
	fclose(fd);
	unlink("tele.load");
	loadpage(num, 1);
}

void sig(int code) {
	switch(code) {
		case SIGUSR1:
			loadpage(num, 0);
			break;
		case SIGUSR2:
			xload();
			break;
	}
}

int main(void) {
	signal(SIGUSR1, SIG_IGN);
	signal(SIGUSR2, SIG_IGN);
	FILE* tele = fopen("tele.pipe", "r");
	loadpage(100, 1);
	char buf[33];
	int inum = 0;
	for(;;) {
		fgets(buf, 32, tele);
		buf[strlen(buf) - 1] = 0;
		if(strlen(buf) == 1 && *buf >= '0' && *buf <= '9') {
			inum = 10*inum + *buf - '0';
			fore(255, 255, 255);
			back(0, 0, 0);
			move(0, 76);
			printf(inum < 10 ? "%d__" : "%d_", inum);
			draw();
		} else if(strcmp(buf, "exit") == 0) {
			break;
		} else if(strcmp(buf, "F2") == 0) {
			inum = 101;
		} else if(strcmp(buf, "F3") == 0) {
			inum = 200;
		} else if(strcmp(buf, "F4") == 0) {
			inum = 300;
		} else if(strcmp(buf, "F1") == 0) {
			inum = 400;
		} else if(strcmp(buf, "play") == 0) {
			inum = num;
		} else if(strcmp(buf, "rewind") == 0) {
			inum = 100;
		} else if(strcmp(buf, "left") == 0) {
			inum = num - 1;
		} else if(strcmp(buf, "right") == 0) {
			inum = num + 1;
		} else if(strcmp(buf, "up") == 0) {
			inum = num % 100 ? num - (num % 100) : num - 100;
		} else if(strcmp(buf, "down") == 0) {
			inum = num + 100 - (num % 100);
		} else {
			printf("Reading invalid key %s\n", buf);
		}
		if(inum >= 100) {
			if(loadpage(inum, 1)) {
				num = inum;
			}
			inum = 0;
		}
	}
	fclose(tele);
	return 0;
}
