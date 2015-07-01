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

Obviously, this would be better off in a Controller class, or several. But this shows the agility of Slim combined with xPDO very well.

### Quick and Dirty News using YAML Front Matter

Another thing I've been wanting to do is provide a simple Jekyll-like news section where I could write stories in Markdown files and have them automatically summarized, listed, and available on the site. Without needing a content management system. So after a quick search via [Packagist](https://packagist.org) for `YAML front matter` libraries, I settled on [FrontYAML](https://github.com/mnapoli/FrontYAML) from [Matthieu Napoli](https://github.com/mnapoli), a great PHP developer at [Piwik Pro](https://piwik.pro/). With that and [Symfony Finder](http://symfony.com/doc/current/components/finder.html), I was able to quickly put together a news section where I could simply add new year and month directories containing `.md` files with, well, YAML front matter. Here's the code:

    $app->group('/news', function() use ($app) {
        $app->get('/', function() use ($app) {
            $finder = new \Symfony\Component\Finder\Finder();
            $articles = $finder->files()->in($app->view()->getTemplatesDirectory() . '/articles/*/*/')
                ->name('*.md')
                ->sortByModifiedTime();
    
            $parser = new \Mni\FrontYAML\Parser();
    
            $yaml = [];
            /** @var \Symfony\Component\Finder\SplFileInfo $article */
            foreach (new LimitIterator($articles->getIterator(), 0, 5) as $article) {
                $document = $parser->parse($article->getContents());
                $yaml[] = $document->getYAML();
            }
    
            (new \Tacit\Views\View($app))->handle('news.twig', ['articles' => $yaml]);
        })->name('News');
        $app->get('/:year/:month/:article/', function($year, $month, $article) use ($app) {
            $parser = new \Mni\FrontYAML\Parser();
    
            $source = dirname(__DIR__) . '/views/' . implode('/', ['articles', $year, $month, $article]) . '.md';
            $document = $parser->parse(file_get_contents($source));
    
            (new \Tacit\Views\View($app))->handle('article.twig', [
                'article' => array_merge($document->getYAML(), ['content' => $document->getContent()])
            ]);
        })->name('NewsArticle');
    });

That's it. List the five most recently modified `.md` files in the `articles/` directory in the News route, and render full articles with the NewsArticle route. Instant news section. Minimalism at its finest, in my opinion.

I'd like to thank Patrick Rice from [SkyToaster](https://skytoaster.com/), Matthew Jones from [IdeaBank Marketing](https://www.ideabankmarketing.com/), and Oliver Haase-Lobinger from [Mind Effects Design + Media](http://www.mindeffects.de/) for their graphic design contributions. Every contribution counts in Open Source, and these fine [MODX](http://modx.com/) community members helped me focus on getting this previously pathetic site replaced quickly. Much appreciated folks!
