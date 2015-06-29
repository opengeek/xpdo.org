---
year: "2015"
month: "06"
slug: new-xpdo-site
title: A New xPDO Website
subtitle: xPDO.org Gets a Face-lift
summary: We have launched a new website for xPDO.org using Slim, xPDO and a few free libraries available via Composer. It was way overdue and served as a great opportunity to show off xPDO in a context other than MODX.
---
We have launched a new website for xPDO.org using [Slim](http://www.slimframework.com/), xPDO and a few free libraries available via [Composer](https://getcomposer.org/). It was way overdue and served as a great opportunity to show off xPDO in a context other than [MODX](http://modx.com/). The repository for this site is public and [available at GitHub](https://github.com/opengeek/xpdo.org) if you want to see how it has been developed.


### An Opportunity to Show Off xPDO

Neglected for many years, this site and its desperate need for a makeover seemed a perfect opportunity to show how easy it is to use xPDO in a project. For this I used the latest stable Slim Framework release (2.6.2 as of writing) and the readily available SQLite3 database engine. I just needed a list of xPDO releases to render on the site and keep track of the number of downloads. After a few minutes planning my attack, I dove in and created a [schema for my releases](https://github.com/opengeek/xpdo.org/blob/master/data/schema.sqlite.xml). That's when I ran into my first issue working with a schema when requiring xPDO via Composer: the bin/xpdo script in the vendor installation of xpdo did not work from the root of my project when it was used as a dependency. The quick solution was to make a copy of the script in a local bin/ directory of the project. The long-term solution is to make the script executable from the vendor location.

With that solved I quickly generated my new model classes and prepared to start writing the download routes. Here is what I came up with:

    $app->group('/downloads', function() use ($app) {
        $app->get('/', function() use ($app) {
            /** @var \xPDO\xPDO $db */
            $db = $app->container->get('db');
    
            /** @var \xPDO\DotOrg\Releases\Release[] $releases */
            $releases = $db->getCollection('xPDO\DotOrg\Releases\Release', $db->newQuery(
                    'xPDO\DotOrg\Releases\Release', ['stability' => 'stable']
                )->sortby('version_major', 'DESC')
                ->sortby('version_minor', 'DESC')
                ->sortby('version_patch', 'DESC')
                ->sortby('stability', 'DESC')
                ->sortby('stability_version', 'DESC')
            );
    
            $app->render('downloads.twig', ['releases' => $releases]);
        })->name('Downloads');
    
        $app->get('/:release/', function($release) use ($app) {
            /** @var \xPDO\xPDO $db */
            $db = $app->container->get('db');
    
            /** @var \xPDO\DotOrg\Releases\Release $releaseData */
            $releaseData = $db->getObject('xPDO\DotOrg\Releases\Release', ['signature' => $release]);
            if ($releaseData === null) {
                $app->notFound();
            }
    
            $app->render('download.twig', ['release' => $releaseData]);
        })->name('Download');
    
        $app->get('/:release/download/', function($release) use ($app) {
            /** @var \xPDO\xPDO $db */
            $db = $app->container->get('db');
    
            /** @var \xPDO\DotOrg\Releases\Release $releaseData */
            $releaseData = $db->getObject('xPDO\DotOrg\Releases\Release', ['signature' => $release]);
            if ($releaseData === null) {
                $app->notFound();
            }
    
            $releaseData->set('downloads', $releaseData->get('downloads') + 1);
            $releaseData->save();
    
            $app->redirect($releaseData->get('url'));
        })->name('DownloadRelease');
    });

