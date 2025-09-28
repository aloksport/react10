import ReactGA from 'react-ga4';
import { useEffect } from 'react';

const TRACKING_ID = 'G-PHPLSB0E70'; // Replace with your Measurement ID

export const useAnalytics = () => {
  useEffect(() => {
    ReactGA.initialize(TRACKING_ID);
    ReactGA.send({ hitType: 'pageview', page: window.location.pathname + window.location.search });
  }, []);
};