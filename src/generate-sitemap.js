const { SitemapStream, streamToPromise } = require('sitemap');
const { createWriteStream } = require('fs');

const links = [
  { url: '/', changefreq: 'daily', priority: 1.0 },
  { url: '/about', changefreq: 'monthly', priority: 0.7 },
  { url: '/contact', changefreq: 'monthly', priority: 0.7 },
  // add all your routes
];

const stream = new SitemapStream({ hostname: 'http://springtown.in/' });
streamToPromise(stream).then(data =>
  require('fs').writeFileSync('./public/sitemap.xml', data.toString())
);

links.forEach(link => stream.write(link));
stream.end();
