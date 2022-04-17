# Installation Guide

## Install Project Core

```bash
mkdir <YOU_PROJECT>
cd <YOU_PROJECT>
git init
mkdir protected
git submodule add git@github.com:the-sashko/sonder-core.git protected/framework
/bin/bash protected/framework/scripts/install.sh
```

## Edit Configs

Edit config files in `<YOU_PROJECT>/protected/config`

## Deploy Database Dump

Deploy database dump `<YOU_PROJECT>/protected/framework/instal/dump/<YOU_DATABASE_TYPE>.sql`

