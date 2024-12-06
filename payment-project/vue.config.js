module.exports = {
  devServer: {
    proxy: {
      '/api': {
        target: 'https://10.20.1.11:9000',
        changeOrigin: true,
        secure: false,
        pathRewrite: { '^/api': '' },
      },
    },
  },
};
