CONTENT := $(wildcard content/*.php)
SYMLINKS := $(notdir $(CONTENT))

# Commands
MAKE_LINK = ln -s
DATABASE_INSTALL = mysql -p
DATABASE_DUMP = mysqldump -p

# Information about the database
DATABASE_SERVER = oniddb.cws.oregonstate.edu
DATABASE_USER = smartw-db
DATABASE_NAME = smartw-db
DATABASE_DUMP_FILE = web-database.sql

DATABASE_SERVER = localhost
DATABASE_USER = smartw
DATABASE_NAME = web
DATABASE_DUMP_FILE = web-database.sql

install: superclean links

links:
	for link in $(SYMLINKS) ; do \
		$(MAKE_LINK) page.php $$link ; \
	done

dump-database:
	$(DATABASE_DUMP) -h $(DATABASE_SERVER) -u $(DATABASE_USER) $(DATABASE_NAME) > $(DATABASE_DUMP_FILE)

install-database:
	cat $(DATABASE_DUMP_FILE) | $(DATABASE_INSTALL) -u $(DATABASE_USER) $(DATABASE_NAME)

clean:
	find . -name "*~" -print | xargs $(RM)

superclean: clean
	$(RM) $(SYMLINKS)
