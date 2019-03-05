SYNC_BRANCHES ?= master 5.0-branch 5.1-branch
SYNC_TAGS ?= $(shell ./tags.php)
UPSTREAM_REPO ?= https://github.com/wordpress/wordpress.git
ORIGIN_REPO ?= git@github.com:presslabs/wordpress.git

.PHONY: sync
sync: fetch # we need to re-spawn make in order to get the fetched tag list
	$(MAKE) -j1 branches
	$(MAKE) -j1 tags

.PHONY: fetch
fetch:
	test -d wordpress || mkdir wordpress
	test -d wordpres/.git || (cd wordpress && git init)
	cd wordpress && git remote show | grep -x origin > /dev/null || git remote add origin $(ORIGIN_REPO)
	cd wordpress && git remote set-url origin $(ORIGIN_REPO)
	cd wordpress && git remote show | grep -x upstream > /dev/null || git remote add upstream $(UPSTREAM_REPO)
	cd wordpress && git remote set-url upstream $(UPSTREAM_REPO)
	cd wordpress && git config --unset-all remote.upstream.fetch || true
	for branch in $(SYNC_BRANCHES) ; do \
		(cd wordpress && git config --add remote.upstream.fetch +refs/heads/$$branch:refs/remotes/upstream/$$branch) \
	done
	cd wordpress && git config --add remote.upstream.fetch +refs/tags/5*:refs/tags/upstream-5*
	cd wordpress && git config --add remote.upstream.fetch +refs/tags/6*:refs/tags/upstream-6*
	cd wordpress && git fetch --tags origin
	cd wordpress && git fetch --no-tags upstream

.PHONY: tags $(SYNC_TAGS)
tags: $(SYNC_TAGS)
$(SYNC_TAGS):
	./sync.php tag $@ $(shell ./tags.php $@)
	cd wordpress && git tag $@
	cd wordpress && git push -u origin tag $@

.PHONY: branches $(SYNC_BRANCHES)
branches: $(SYNC_BRANCHES)
$(SYNC_BRANCHES):
	./sync.php branch $@ upstream/$@
	cd wordpress && git push -f -u origin work-$@:$@
