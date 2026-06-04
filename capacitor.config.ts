import type { CapacitorConfig } from '@capacitor/cli';

const config: CapacitorConfig = {
  appId: 'com.greenhouse.app',
  appName: 'My Green House',
  webDir: 'mobile-dist',
  server: {
    url: 'https://gogreenhouse.my.id',
    cleartext: false,
  },
};

export default config;
