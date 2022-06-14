FLAGS := $(shell pkg-config --cflags --libs ncursesw)

teletext : teletext.c
	gcc -o $@ $^ $(FLAGS)
