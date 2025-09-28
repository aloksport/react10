import { useEffect } from 'react';
import { useLocation } from 'react-router-dom';

export const useMetaTags = ({ title, description, ogTitle, ogDescription, ogImage, twitterCard = 'summary_large_image' }) => {
  const location = useLocation();
  const baseTitle = 'Springtown';

  useEffect(() => {
    // Set document title
    document.title = title ? `${title} | ${baseTitle}` : baseTitle;

    // Helper to set or update meta tag
    const setMetaTag = (name, content, property = 'name') => {
      let meta = document.querySelector(`meta[${property}="${name}"]`);
      if (!meta && content) {
        meta = document.createElement('meta');
        meta.setAttribute(property, name);
        document.head.appendChild(meta);
      }
      if (content) {
        meta.setAttribute('content', content);
      } else if (meta) {
        meta.remove();
      }
    };

    // Set meta tags
    setMetaTag('description', description);
    setMetaTag('og:title', ogTitle, 'property');
    setMetaTag('og:description', ogDescription, 'property');
    setMetaTag('og:image', ogImage, 'property');
    setMetaTag('og:url', `https://springtown.in${location.pathname}`, 'property');
    setMetaTag('og:type', 'website', 'property');
    setMetaTag('twitter:card', twitterCard);
    setMetaTag('twitter:title', ogTitle);
    setMetaTag('twitter:description', ogDescription);
    setMetaTag('twitter:image', ogImage);

    // Cleanup
    return () => {
      setMetaTag('description', '');
      setMetaTag('og:title', '', 'property');
      setMetaTag('og:description', '', 'property');
      setMetaTag('og:image', '', 'property');
      setMetaTag('og:url', '', 'property');
      setMetaTag('og:type', '', 'property');
      setMetaTag('twitter:card', '');
      setMetaTag('twitter:title', '');
      setMetaTag('twitter:description', '');
      setMetaTag('twitter:image', '');
    };
  }, [title, description, ogTitle, ogDescription, ogImage, twitterCard, location.pathname]);
};