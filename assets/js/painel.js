!(function($){
    // menu
    // $('[name="menu-link-1"]').mouseover(function(){
    //     $('[name="menu-icon-1"]').css('color','lightgray');
    //     $('[name="menu-font-1"]').css({'color':'lightgray','text-shadow':'0px 0px 5px lightgray'});
    // });

    // $('[name="menu-link-1"]').mouseout(function(){
    //     $('[name="menu-icon-1"]').css('color','rgb(100,100,100)');
    //     $('[name="menu-font-1"]').css({'color':'rgb(100,100,100)','text-shadow':'none'});
    // });

    // $('[name="menu-link-2"]').mouseover(function(){
    //     $('[name="menu-icon-2"]').css('color','lightgray');
    //     $('[name="menu-font-2"]').css({'color':'lightgray','text-shadow':'0px 0px 5px lightgray'});
    // });

    // $('[name="menu-link-2"]').mouseout(function(){
    //     $('[name="menu-icon-2"]').css('color','rgb(100,100,100)');
    //     $('[name="menu-font-2"]').css({'color':'rgb(100,100,100)','text-shadow':'none'});
    // });

    // $('[name="menu-link-3"]').mouseover(function(){
    //     $('[name="menu-icon-3"]').css('color','rgb(200,200,250)');
    //     $('[name="menu-font-3"]').css({'color':'rgb(200,200,250)','text-shadow':'0px 0px 5px rgb(160,160,250)'});
    // });

    // $('[name="menu-link-3"]').mouseout(function(){
    //     $('[name="menu-icon-3"]').css('color','rgba(150,150,250, 0.7)');
    //     $('[name="menu-font-3"]').css({'color':'rgba(150,150,250, 0.7)','text-shadow':'none'});
    // });

    $('[name="menu-link-4"]').mouseover(function(){
        $('[name="menu-icon-4"]').css('color','rgb(180,180,100)');
        $('[name="menu-font-4"]').css({'color':'rgb(180,180,100)','text-shadow':'0px 0px 5px rgb(180,180,100)'});
    });

    $('[name="menu-link-4"]').mouseout(function(){
        $('[name="menu-icon-4"]').css('color','rgb(120,120,50)');
        $('[name="menu-font-4"]').css({'color':'rgb(120,120,50)','text-shadow':'none'});
    });

    $('[name="menu-link-5"]').mouseover(function(){
        $('[name="menu-icon-5"]').css('color','rgb(180,180,100)');
        $('[name="menu-font-5"]').css({'color':'rgb(180,180,100)','text-shadow':'0px 0px 5px rgb(180,180,100)'});
    });

    $('[name="menu-link-5"]').mouseout(function(){
        $('[name="menu-icon-5"]').css('color','rgb(120,120,50)');
        $('[name="menu-font-5"]').css({'color':'rgb(120,120,50)','text-shadow':'none'});
    });

    // $('[name="menu-link-6"]').mouseover(function(){
    //     $('[name="menu-icon-6"]').css('color','lightgray');
    //     $('[name="menu-font-6"]').css({'color':'lightgray','text-shadow':'0px 0px 5px lightgray'});
    // });

    // $('[name="menu-link-6"]').mouseout(function(){
    //     $('[name="menu-icon-6"]').css('color','rgb(100,100,100)');
    //     $('[name="menu-font-6"]').css({'color':'rgb(100,100,100)','text-shadow':'none'});
    // });

    $('[name="menu-link-7"]').mouseover(function(){
        $('[name="menu-icon-7"]').css('color','rgb(230,180,150)');
        $('[name="menu-font-7"]').css({'color':'rgb(230,180,150)','text-shadow':'0px 0px 5px rgb(230,180,150)'});
    });

    $('[name="menu-link-7"]').mouseout(function(){
        $('[name="menu-icon-7"]').css('color','rgb(180,100,50)');
        $('[name="menu-font-7"]').css({'color':'rgb(180,100,50)','text-shadow':'none'});
    });

    $('[name="menu-link-8"]').mouseover(function(){
        $('[name="menu-icon-8"]').css('color','rgb(150,230,150)');
        $('[name="menu-font-8"]').css({'color':'rgb(150,230,150)','text-shadow':'0px 0px 5px rgb(150,230,150)'});
    });

    $('[name="menu-link-8"]').mouseout(function(){
        $('[name="menu-icon-8"]').css('color','rgb(50,120,50)');
        $('[name="menu-font-8"]').css({'color':'rgb(50,120,50)','text-shadow':'none'});
    });

    $('[name="menu-link-9"]').mouseover(function(){
        $('[name="menu-icon-9"]').css('color','lightgray');
        $('[name="menu-font-9"]').css({'color':'lightgray','text-shadow':'0px 0px 5px lightgray'});
    });

    $('[name="menu-link-9"]').mouseout(function(){
        $('[name="menu-icon-9"]').css('color','rgb(100,100,100)');
        $('[name="menu-font-9"]').css({'color':'rgb(100,100,100)','text-shadow':'none'});
    });

    // content
    $('.coin-submit').click(function(){
        var url = '/coin/' + $(this).data('coin');
        $(location).attr('href',url);
    });

    $('.btn-coin').click(function(){
        var url = '/coin/' + $(this).data('coin');
        $(location).attr('href',url);
    });

    $('[data-target="#showitem"]').click(function(){
        $('[name="aionId"]').val($(this).data('id'));
        $('[name="image"]').attr('src',"build/img/shop/info/"+$(this).data('id')+".png");

        var amount = $(this).data('set');
        
        if(amount == 1){
            $('#amount').show();
        }else{
            $('#amount').hide();
        }
    })

    $('[data-target="#vipMember"]').click(function(){
        $('[name="aionId"]').val($(this).data('id'));
        $('[name="msg"]').text('Voce está prestes a adquirir o pacote '+($(this).data('set'))+'. Você tem certeza que deseja realizar essa compra?');

        
    })

    $('[data-target="#editShopItem"]').click(function(){
        $('[name="id"]').val($(this).data('id'));
        var camount = $(this).data('set');
        
        if(camount == 1){
            $('#amount').show();
        }else{
            $('#amount').hide();
        }
    })

    $('[data-target="#editFeed"]').click(function(){
        $('[name="id"]').val($(this).data('id'));
        $('[name="direct"]').val($(this).data('direct'));
    })

    $('[data-target="#messageView"]').click(function(){
        $('[name="date"]').text($(this).data('time'));
        $('[name="title"]').text($(this).data('set'));
        $('[name="text"]').text($(this).data('text'));
    })

    $('[data-target="#feedReport"]').click(function(){
        $('[name="formreport"]').attr('action',$(this).data('url'));
        $('[name="direct"]').val($(this).data('direct'));
        $('[name="post"]').val($(this).data('post'));
    })

    $('[data-target="#editItem"]').click(function(){
        $('[name="id"]').val($(this).data('id'));
        $('[name="aion"]').text($(this).data('id'));
        $('[name="editlevel"]').val($(this).data('level'));
        $('[name="editname"]').val($(this).data('name'));
        $('[name="editprice"]').val($(this).data('price'));
        $('[name="editamount"]').val($(this).data('amount'));
        $('[name="editdiscount"]').val($(this).data('discount'));
        $('[name="editbbcode"]').val($(this).data('bbcode'));
    })

    $('[data-target="#deleteItem"]').click(function(){
        $('[name="id"]').val($(this).data('id'));
        $('[name="aion"]').text($(this).data('name'));
    })

    $('[data-target="#promoItem"]').click(function(){
        $('[name="id"]').val($(this).data('id'));
    })

    $('[data-target="#editType"]').click(function(){
        $('[name="id"]').val($(this).data('id'));
    })

    $('[data-target="#deleteType"]').click(function(){
        $('[name="id"]').val($(this).data('id'));
        $('[name="aion"]').text($(this).data('name'));
    })

    $('[data-target="#notifyReport"]').click(function(){
        $('[name="id"]').val($(this).data('id'));
        $('[name="pid"]').val($(this).data('pid'));
        $('[name="formnotify"]').attr('action',$(this).data('url'));
    })

    $('[data-target="#unLock"]').click(function(){
        $('[name="id"]').val($(this).data('id'));
    })

    $('[data-target="#carouselEdit"]').click(function(){
        $('[name="id"]').val($(this).data('id'));
    })

    $('[data-target="#carouselActive"]').click(function(){
        $('[name="id"]').val($(this).data('id'));
    })

    $('[data-target="#carouselDelete"]').click(function(){
        $('[name="id"]').val($(this).data('id'));
    })

    $('[data-target="#noticeEdit"]').click(function(){
        $('[name="id"]').val($(this).data('id'));
    })

    $('[data-target="#noticeActive"]').click(function(){
        $('[name="id"]').val($(this).data('id'));
    })

    $('[data-target="#noticeDelete"]').click(function(){
        $('[name="id"]').val($(this).data('id'));
    })

    $('[data-target="#resourceEdit"]').click(function(){
        $('[name="id"]').val($(this).data('id'));
    })

    $('[data-target="#resourceActive"]').click(function(){
        $('[name="id"]').val($(this).data('id'));
    })

    $('[data-target="#resourceDelete"]').click(function(){
        $('[name="id"]').val($(this).data('id'));
    })

    $('[data-target="#reenviarItem"]').click(function(){
        $('[name="id"]').val($(this).data('id'));
    })

    $('[data-target="#confirmCoin"]').click(function(){
        $('[name="id"]').val($(this).data('id'));
        $('[name="user"]').val($(this).data('user'));
    })

    $('[data-target="#removeCoin"]').click(function(){
        $('[name="id"]').val($(this).data('id'));
    })

    $('[data-target="#itemPresente"]').click(function(){
        $('[name="id_gift"]').val($(this).data('id'));
    })

    $('#senha-submit').click(function(e){
        e.preventDefault();
        var pwd = $('#pwd').val();
        var cpwd = $('#cpwd').val();
        if(pwd != cpwd){
            $('#msg').text('As senhas estão diferentes.')
        }else{
            $('#form-redefinir').submit();
        }
    })

    //mercado pago script
    

})(jQuery);