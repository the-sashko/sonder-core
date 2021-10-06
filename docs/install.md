# Installation Guide

## Install Project Core

```bash
mkdir <YOU_PROJECT>
cd <YOU_PROJECT>
git init
mkdir protected
git submodule add git@github.com:the-sashko/sonder-core.git protected/core
/bin/bash protected/core/scripts/install.sh
```

## Edit Configs

Edit config files in `<YOU_PROJECT>/protected/config`

## Deploy Database Dump

Deploy database dump `<YOU_PROJECT>/protected/core/instal/dump/<YOU_DATABASE_TYPE>.sql`

