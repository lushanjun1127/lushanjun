  <script>
    // 页面加载完成后移除加载动画 - 优化过渡效果
    window.addEventListener('load', function() {
      setTimeout(() => {
        document.body.classList.add('loaded');
        // 延迟移除预加载元素，等待淡出动画完成
        setTimeout(() => {
          const preloader = document.querySelector('.preloader');
          if (preloader) {
            preloader.style.display = 'none';
          }
        }, 300); // 与 CSS transition 时间匹配
      }, 100);
    });
      
    // 监听 DOM 内容加载完成事件
    document.addEventListener('DOMContentLoaded', function() {
      // 如果页面已经完全加载，则直接移除加载动画
      if (document.readyState === 'complete') {
        document.body.classList.add('loaded');
      }
    });
  </script>
</body>
  <script src="<?=fileUri()?>/assets/js/theme.js?v=<?=THEME_VERSION?>"></script>
  <script src="<?=fileUri()?>/assets/static/nprogress/nprogress.js?v=<?=THEME_VERSION?>"></script>
  <script>
    NProgress.start();
    $(document).ready(()=>{
      NProgress.done();
    });
    NProgress.remove();
  </script>
</html>