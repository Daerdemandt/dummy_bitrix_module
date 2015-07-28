#!/usr/bin/env bash
# Shouldn't we replace it with makefile?
TARGET="dummymodule"
echo "Packing $TARGET"
tar --verbose --create --gzip --file=${TARGET}.tar.gz $TARGET
