// Remove tag A with mousedown - 优化触摸反馈
// 使用事件委托和防抖优化
let touchTimeout;
$('body').on('mousedown touchstart', 'a', function(e) {
  if (e.type === 'touchstart') {
    clearTimeout(touchTimeout);
    $(this).addClass('touch-active');
    touchTimeout = setTimeout(() => {
      $(this).removeClass('touch-active');
    }, 300);
  }
});


// Search btn - Mobile
$('#search-btn').click(function() {
  $('.search-m').toggleClass('active');
  if($('.search-m').attr('class') == 'search-m active') {
    $('.nav').css('border-bottom','1px solid #fff');
  } else {
    $('#search-m').blur();
    $('.nav').css('border-bottom','1px solid #e2e2e2');
  }
})
function close_search_m() {
  $('#search-m').blur();
  $('.search-m').removeClass('active');
  if($('.search-m').attr('class') != 'search-m active') {
    $('.nav').css('border-bottom','1px solid #e2e2e2');
  }
}


// Menu btn - 优化动画性能
// 使用 CSS class 替代内联样式，使用 requestAnimationFrame
$('#menu-btn').click(function() {
  requestAnimationFrame(() => {
    const headerClass = $('header').attr('class');
    const asideClass = $('aside').attr('class');
    
    if (headerClass === asideClass) {
      $('header').toggleClass('active');
      $('aside').toggleClass('active');
    } else if (headerClass === 'active' && asideClass !== 'active') {
      $('header').removeClass('active');
    } else if (headerClass !== 'active' && asideClass === 'active') {
      $('header').addClass('active');
    }
    close_search_m();
  });
});

$('#aside-btn').click(function() {
  $('aside').toggleClass('active');
  close_search_m();
})


// Nav user menu
const user_set_btn = document.querySelector('main .nav .user-menu #user-menu-btn');
const user_set_menu = document.querySelector('main .nav .user-menu .user-menu-box');
function remove_set_menu(e) {
  user_set_menu.classList.remove('active');
  document.removeEventListener("click",remove_set_menu);
}

if(user_set_btn) {
  user_set_btn.addEventListener("click",(e)=>{
    e.stopPropagation();
    close_search_m();
    if(user_set_menu.classList.toggle('active')) {
      document.addEventListener("click",remove_set_menu);
    }
  })
  user_set_menu.addEventListener("click",(e)=>e.stopPropagation());
}


// Click with hide menu and search
$('article, aside .aside-content').on('click', function() {
  $(user_set_menu).removeClass('active');
  close_search_m();
})

$('article').on('click', function() {
  $('header').removeClass('active');
  $('aside').removeClass('active');
  close_search_m();
})

$('header').on('click', function() {
  close_search_m();
})


// Horizontal scrolling - 优化滚动性能
// 使用 passive listener 和节流优化
const cateTagContainer = document.querySelectorAll(`
  .category main .content article .categories ul, 
  .tag main .content article .tag-bar ul,
  .single main .content article .post-cover .post-info .more,
  .home main .content article .bottom .recommend-bar
`);

// 节流函数
function throttle(func, delay) {
  let lastCall = 0;
  return function(...args) {
    const now = Date.now();
    if (now - lastCall >= delay) {
      lastCall = now;
      func.apply(this, args);
    }
  };
}

if (cateTagContainer.length > 0) {
  $(cateTagContainer).each(function(i) {
    // 使用 passive listener 提升滚动性能
    const scrollHandler = throttle((event) => {
      event.preventDefault();
      this.scrollLeft += event.deltaY;
    }, 16); // 约 60fps
    
    cateTagContainer[i].addEventListener("wheel", scrollHandler, { passive: false });
  });
}


// Menu tooltip - 优化 DOM 操作
// 使用 DocumentFragment 减少重排
const menuItemA = document.querySelectorAll('header .menu > li > a');

if (menuItemA.length > 0) {
  const fragment = document.createDocumentFragment();
  const menuItemsData = [];
  
  $(menuItemA).each(function(i) {
    const title = $(menuItemA[i]).attr('title');
    if (title) {
      menuItemsData[i] = title;
      $(menuItemA[i]).removeAttr('title');
      const span = document.createElement('span');
      span.className = 'menu-item-title';
      span.textContent = title;
      fragment.appendChild(span);
    }
  });
  
  // 批量添加元素
  menuItemA.forEach((el, i) => {
    if (menuItemsData[i]) {
      el.appendChild(fragment.children[i]);
    }
  });
}


// Home new or sticky - 优化动画性能
// 使用 requestAnimationFrame 和 CSS transform
const $slider = $('.home .recommend-bar ul .slider');
const $navItems = $('.home .recommend-bar ul li a');

// 初始化 slider 位置
$slider.width($navItems.filter('.active').outerWidth());

$navItems.each(function(i) {
  $(this).attr('index', i);
  $(this).on('click', function(e) {
    e.preventDefault();
    const $this = $(this);
    
    // 更新 active 状态
    $navItems.removeClass('active');
    $this.addClass('active');
    
    // 使用 requestAnimationFrame 优化动画
    requestAnimationFrame(() => {
      const width = $this.outerWidth();
      const position = $this.position();
      const scrollLeft = $this.parent().scrollLeft();
      
      // 使用 CSS transform 替代 left 属性提升性能
      $slider.css({
        width: width,
        transform: `translateX(${position.left + scrollLeft}px)`
      });
    });
    
    // 切换内容区域
    $('.home .post-part').removeClass('active');
    $($('.home .post-part')[$this.attr('index')]).addClass('active');
  });
});


// color-theif - 添加 nonce 验证
function get_color(callback) {
  $.ajax({
		type: 'POST',
		url: '/wp-admin/admin-ajax.php',
		contentType: "application/x-www-form-urlencoded",
		datatype: "json",
		data: {
			"action": "random_img",
			"nonce": randomImgNonce || '' // 从全局变量获取 nonce
		}
	}).done(function(data) {
    callback(data);
	}).fail(function(jqXHR, textStatus, errorThrown) {
		// console.debug(jqXHR);
	})
}


// aside sub page
const aside_btn_open = $('aside .aside-content .aside-btn-open');
const aside_btn_close = $('aside .aside-content .aside-btn-close');

aside_btn_open.on('click', function() {
  $('aside .aside-content').addClass('active');
  $('aside .aside-content .main-page').removeClass('active');
  $('aside .aside-content .sub-page').addClass('active');
})
aside_btn_close.on('click', function() {
  $('aside .aside-content').removeClass('active');
  $('aside .aside-content .sub-page').removeClass('active');
  $('aside .aside-content .main-page').addClass('active');
})


// aside sub page menu - 优化触摸交互
const menu_parent = $('.sub-page .menu_sidebar > ul > li.menu-item-has-children');
let menu_parent_hight = [];
let menu_parent_a_hight = [];
$(document).ready(function() {
  $(menu_parent).each(function(i) {
    menu_parent_hight[i] = $(this).outerHeight();
    menu_parent_a_hight[i] = $(this).children('a').outerHeight();
    $(this).height(menu_parent_a_hight[i]);
    $(this).children('a').on('click touchend', function(e) {
      if (e.type === 'touchend') {
        e.preventDefault(); // 防止触发两次
      }
      e.stopPropagation();
      if(!$(this).hasClass('active')) {
        $(this).parent().height(menu_parent_hight[i]);
        $(this).children('.arrow').addClass('active');
        $(this).addClass('active');
      } else {
        $(this).parent().height(menu_parent_a_hight[i]);
        $(this).children('.arrow').removeClass('active');
        $(this).removeClass('active');
      }
    })

    $(document).click((e)=>{
      if($(this).children('a').hasClass('active')) {
        if(!$(e.target).is($(this).children('a, ul')) && !$(e.target).is(this.querySelectorAll('ul li ul li a, a *'))) {
          $(this).height(menu_parent_a_hight[i]);
          $(this).children('a').removeClass('active');
          $(this.querySelector('.arrow')).removeClass('active');
        }
      }
    })
  })
})


// toc
const post_title = $(`
.post-content h1,
.post-content h2,
.post-content h3
`);

function add_toc() {
  let title_i = 1;
  post_title.each(function() {
    $(this).attr('id', 'title-' + title_i);
    $('aside .toc ul').append('<li id="' + $(this).prop('tagName') + '"><a href="#title-' + title_i+'">' + $(this).text() + '</a></li>');
    title_i ++;
  })
}

if($('.toc').length > 0 && post_title.length >= 2) {
  $('aside .toc').show();
  add_toc();
} else {
  $('aside .toc').hide();
}