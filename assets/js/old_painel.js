!(function($){
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



})(jQuery);