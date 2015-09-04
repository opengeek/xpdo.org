<?php
/*
 * This file is part of the xpdo.org package.
 *
 * Copyright (c) Jason Coward <jason@opengeek.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

$app->notFound(function() use ($app) {
    $app->render('404.twig');
});

$app->get('/', function() use ($app) {
    $app->render('index.twig');
})->name('Home');

$app->get('/about/', function() use ($app) {
    $app->render('about.twig');
})->name('About');

$app->group('/news', function() use ($app) {
    $app->get('/', function() use ($app) {
        $sort = function ($a, $b) {
            return ($b->getMTime() - $a->getMTime());
        };

        $finder = new \Symfony\Component\Finder\Finder();
        $articles = $finder->files()->in($app->view()->getTemplatesDirectory() . '/articles/*/*/')
            ->name('*.md');

        $parser = new \Mni\FrontYAML\Parser();

        $yaml = [];
        /** @var \Symfony\Component\Finder\SplFileInfo $article */
        foreach (new LimitIterator((new \Symfony\Component\Finder\Iterator\SortableIterator($articles->getIterator(), $sort))->getIterator(), 0, 5) as $article) {
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
