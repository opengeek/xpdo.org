---
year: "2015"
month: "09"
slug: new-branching-strategy
title: A New Branching Strategy for xPDO
subtitle: Adopting a simplified, version-specific branching strategy
summary: In an attempt to simplify the contribution process for xPDO and bring it inline with the new MODX Revolution strategy, I am introducing a similar new branching strategy based on major and minor versions.
---
In an attempt to simplify the contribution process for xPDO and bring it inline with the [new MODX Revolution strategy](http://modx.com/blog/2015/09/03/a-new-branching-strategy-for-modx-revolution/), I am introducing a similar new branching strategy based on major and minor versions.

## Major and Minor Version-Specific Branches

Instead of a permanent `master` and `develop` branch that continually change meaning as the stable releases are tagged, this simplified strategy maintains a development branch for every major release, e.g. `2.x`, `3.x`, etc., and a development branch for the current stable minor releases within each major version, e.g. `2.4.x`. The major-version development branch represents changes targeted for the next minor release, while the minor-version development branch contains changes for the next patch release. Following SemVer, this means features always go to a major-version branch and bug fixes should go to a minor-version branch.

