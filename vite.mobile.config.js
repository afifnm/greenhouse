import { defineConfig, loadEnv } from 'vite';

export default defineConfig(({ mode }) => {
  const env = loadEnv(mode, '.', 'VITE_');
  return {
    root: 'mobile-src',
    define: {
      'import.meta.env.VITE_API_URL': JSON.stringify(env.VITE_API_URL ?? 'https://gogreenhouse.my.id/api/v1'),
    },
    build: {
      outDir: '../mobile-dist',
      emptyOutDir: true,
    },
  };
});
