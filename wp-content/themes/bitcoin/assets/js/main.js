var App = {
  btcSlider: function () {
    if (jQuery('.coin_news').length) {
      var splide = new Splide('.coin_news', {
        type: 'loop',
        perPage: 1,
        autoWidth: true,
        autoScroll: {
          speed: 0.5,
        },
        arrows: false,
        pagination: false,
        breakpoints: {
          991: {
            perPage: 1,
          },
        }
      });
      splide.mount(window.splide.Extensions);
    }
    // if (jQuery('.trade_live').length) {
    //   var tsplide = new Splide('.trade_live', {
    //     type: 'loop',
    //     perPage: 1,
    //     autoWidth: true,
    //     autoScroll: {
    //       speed: 0.2,
    //     },
    //     arrows: false,
    //     pagination: false,
    //     breakpoints: {
    //       991: {
    //         perPage: 1,
    //       },
    //     }
    //   });
    //   tsplide.mount(window.splide.Extensions);
    // }
    if (jQuery('.slider_samecate').length) {
      jQuery('#relateTab .nav-link').click(function () {
        $tab = jQuery(this).data('arrow');
        console.log($tab);
        jQuery('.arrow_slider').removeClass('active');
        jQuery('.' + $tab).addClass('active');
      });
      jQuery('.slider_samecate').each(function () {
        $arrows = jQuery(this).data('arrow');
        jQuery(this).slick({
          slidesToShow: 3,
          slidesToScroll: 1,
          infinite: true,
          rows: 2,
          arrows: true,
          prevArrow: jQuery('.' + $arrows + ' .prev_slider a'),
          nextArrow: jQuery('.' + $arrows + ' .next_slider a'),
          responsive: [
            {
              breakpoint: 991,
              settings: {
                slidesToShow: 2,
                slidesToScroll: 1,
                rows: 3,
              }
            },
          ]
        });
      });
    }
  },
  searchClick: function () {
    jQuery('.search_btn a').click(function (e) {
      e.preventDefault();
      jQuery('.search_table').toggleClass('active');
      jQuery('.overlay_menu').toggleClass('active');
    });
    jQuery('.close_search').click(function () {
      jQuery('.search_table').removeClass('active');
      jQuery('.overlay_menu').removeClass('active');
    });
    jQuery('.overlay_menu').click(function () {
      jQuery('.search_table').removeClass('active');
      jQuery('.overlay_menu').removeClass('active');
    });
  },
  menuMobile: function () {
    jQuery('.hamburger_btn').click(function (e) {
      e.preventDefault();
      jQuery('.hamburger-icon').toggleClass('open');
      jQuery('#menu_mobile').toggleClass('open');
      jQuery('.overlay_menu').toggleClass('is-active');
    });
    jQuery('.overlay_menu').click(function () {
      jQuery('.hamburger-icon').removeClass('open');
      jQuery('#menu_mobile').removeClass('open');
      jQuery('.overlay_menu').removeClass('is-active');
    });
    jQuery('#menu_mobile .menu_site li.menu-item-has-children').click(function (e) {
      e.stopPropagation();
      jQuery(this).toggleClass('show_submenu');
      jQuery(this).find('>.sub-menu').stop().slideToggle('fast');
    });
    jQuery('#menu_mobile .menu_profile > ul li').click(function (e) {
      e.stopPropagation();
      jQuery(this).toggleClass('show_submenu');
      jQuery(this).find('>.sub-menu').stop().slideToggle('fast');
    });
  },
  tablePrice: function () {
    jQuery('.price_sidebar > ul > li > a').click(function (e) {
      e.preventDefault();
      jQuery('.price_sidebar > ul > li').removeClass('active');
      jQuery(this).parent('li').addClass('active');
      jQuery('.price_sidebar > ul > li > ul').slideUp();
      jQuery(this).parent('li').find('>ul').slideDown();

    })
    if (jQuery('#priceTable').length) {
      const table = new DataTable('#priceTable', {
        columnDefs: [
          {
            searchable: false,
            orderable: false,
            targets: 0
          },
        ],
        lengthMenu: [
          [100, 150, -1],
          [100, 150, 'All']
        ],
        language: {
          info: 'Displaying _PAGE_-_PAGES_ of _MAX_ total coins',
          infoEmpty: 'No records available',
          infoFiltered: '(filtered from _MAX_ total records)',
          lengthMenu: 'Display _MENU_ records per page',
          zeroRecords: 'Nothing found - sorry'
        },
        searching: false,
        scrollX: true
      });
      table
        .on('order.dt search.dt', function () {
          let i = 1;
          table
            .cells(null, 0, { search: 'applied', order: 'applied' })
            .every(function (cell) {
              this.data(i++);
            });
        })
        .draw();
    }
  },
  activeOnScroll: function () {
    if (jQuery('.people_id').length) {
      jQuery(window).scroll(function () {
        var scrollPos = jQuery(document).scrollTop(); // Vị trí cuộn hiện tại

        jQuery('.people_id a').each(function () {
          var targetId = jQuery(this).attr('href'); // Lấy giá trị của thuộc tính href
          var targetPos = jQuery(targetId).offset().top - 100; // Vị trí của phần tử có ID tương ứng

          // Kiểm tra nếu vị trí cuộn lớn hơn hoặc bằng vị trí của phần tử
          // và nhỏ hơn vị trí của phần tử tiếp theo (nếu có)
          if (scrollPos >= targetPos && scrollPos < targetPos + jQuery(targetId).height()) {
            jQuery('.people_id li').removeClass('active'); // Loại bỏ lớp "active" từ tất cả các phần tử
            // var cumulativeHeight = 82;
            // ul = jQuery('.jrule__nav');
            // liIndex = jQuery(this).parent('li').index();
            // ul.find('li').each(function(index) {
            //     if (index < liIndex) {
            //         cumulativeHeight -= jQuery(this).outerHeight(true);
            //     }
            // });
            // jQuery('.jrule__nav').attr('style','top:'+cumulativeHeight+'px');
            jQuery(this).parent('li').addClass('active'); // Thêm lớp "active" cho phần tử tương ứng
          }
        });
      });
    }
    if (jQuery('.menu_coin').length) {
      jQuery(window).scroll(function () {
        var scrollPos = jQuery(document).scrollTop(); // Vị trí cuộn hiện tại

        jQuery('.menu_coin ul li a').each(function () {
          var targetId = jQuery(this).attr('href'); // Lấy giá trị của thuộc tính href
          var targetPos = jQuery(targetId).offset().top - 100; // Vị trí của phần tử có ID tương ứng

          // Kiểm tra nếu vị trí cuộn lớn hơn hoặc bằng vị trí của phần tử
          // và nhỏ hơn vị trí của phần tử tiếp theo (nếu có)
          if (scrollPos >= targetPos && scrollPos < targetPos + jQuery(targetId).height()) {
            jQuery('.menu_coin li').removeClass('active'); // Loại bỏ lớp "active" từ tất cả các phần tử
            // var cumulativeHeight = 82;
            // ul = jQuery('.jrule__nav');
            // liIndex = jQuery(this).parent('li').index();
            // ul.find('li').each(function(index) {
            //     if (index < liIndex) {
            //         cumulativeHeight -= jQuery(this).outerHeight(true);
            //     }
            // });
            // jQuery('.jrule__nav').attr('style','top:'+cumulativeHeight+'px');
            jQuery(this).parent('li').addClass('active'); // Thêm lớp "active" cho phần tử tương ứng
          }
        });
      });
    }
  },
  validateForm: function(){
    (function () {
        'use strict'
        // Fetch all the forms we want to apply custom Bootstrap validation styles to
        var forms = document.querySelectorAll('.needs-validation')
        // Loop over them and prevent submission
        Array.prototype.slice.call(forms)
          .forEach(function (form) {
            form.addEventListener('submit', function (event) {
              if (!form.checkValidity()) {
                event.preventDefault()
                event.stopPropagation()
              }
              form.classList.add('was-validated')
            }, false)
          })
    })();
    if (jQuery('.valid_group').length) {
      jQuery('.valid_group').each(function() {
          let $input = jQuery(this).find('.form-control');
          
          // Kiểm tra xem $input có tồn tại không trước khi xử lý
          if ($input.length) {
              // Hàm cập nhật class
              function updateHasValue() {
                  $input.toggleClass('hasValue', $input.val().length > 0);
              }
  
              // Gọi hàm khi tải trang
              updateHasValue();
  
              // Gắn sự kiện input
              $input.on('input', updateHasValue);
          }
      });
      jQuery('.showPass').click(function(){
        $passInput = jQuery(this).parent('.valid_group').find('input');
        jQuery(this).toggleClass('fa-eye fa-eye-slash');
        if ($passInput.attr('type') === 'password') {
          $passInput.attr('type', 'text');
        } else {
          $passInput.attr('type', 'password');
        }
      });
    }
    if(jQuery('.password-container').length){
      const passwordInput = document.querySelector('#password-input');
      const conditions = document.querySelectorAll('.conditions li');

        passwordInput.addEventListener('input', function() {
            const password = this.value;
            
            // Kiểm tra các điều kiện
            const hasLength = password.length >= 8;
            const hasLowercase = /[a-z]/.test(password);
            const hasUppercase = /[A-Z]/.test(password);
            const hasNumber = /[0-9]/.test(password);
            const hasSpecial = /[!@#$%^&*]/.test(password);
            
            // Đếm số điều kiện đạt được trong 4 loại
            const conditionsMet = [
                hasLowercase,
                hasUppercase,
                hasNumber,
                hasSpecial
            ].filter(Boolean).length;
            
            // Cập nhật class valid cho từng điều kiện
            conditions.forEach(condition => {
                const type = condition.dataset.condition;
                let isValid = false;
                
                switch(type) {
                    case 'length':
                        isValid = hasLength;
                        break;
                    case 'lowercase':
                        isValid = hasLowercase;
                        break;
                    case 'uppercase':
                        isValid = hasUppercase;
                        break;
                    case 'number':
                        isValid = hasNumber;
                        break;
                    case 'special':
                        isValid = hasSpecial;
                        break;
                    case 'threeOfFour':
                        isValid = conditionsMet >= 3;
                        break;
                }
                
                if (isValid) {
                    condition.classList.add('valid');
                } else {
                    condition.classList.remove('valid');
                }
            });
        });
    }
},
};

jQuery(document).ready(function () {
  App.btcSlider();
  App.searchClick();
  App.menuMobile();
  App.tablePrice();
  App.activeOnScroll();
  App.validateForm();
  if (jQuery('#adsModal').length) {
    // const myModal = new bootstrap.Modal('#adsModal', {
    //   keyboard: false
    // });
    // setTimeout(() => {
    //   const modalToggle = document.getElementById('adsModal'); myModal.show(modalToggle);
    // }, 5000);
  }
});
