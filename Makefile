-include cache/Makefile

cache/Makefile: | did_create_page_store bin/generate_makefile
	mkdir -p "$$(dirname $@)"
	bin/generate_makefile > $@

did_create_page_store:
	mkdir pages && cp -pR factory/{css,edit,index.page} pages && \
	sed < factory/edit/.htaccess 's|\$${BASE}|'"$$(pwd)|" > pages/edit/.htaccess && \
	cd pages && git init && git add {css,index.page} && git commit -m 'Initial commit' --author 'Makefile <>' && \
	cd .. && touch $@

.PHONY: clean factory

factory:
	rm -rf cache html pages did_create_page_store

clean:
	rm -rf cache html
