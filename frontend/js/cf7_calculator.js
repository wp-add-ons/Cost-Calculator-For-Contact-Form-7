(function($) {
    "use strict";
    $( document ).ready( function () { 
        $("body").on("click","input.number-format",function(){
            $(this).autoNumeric();
            var data = $(this).autoNumeric("get");
            $(this).val(data);
        })
        $("body").on("change keyup",".wpcf7 input,.wpcf7 select,.wpcf7 textarea",function(e){
            $.cf7_formulas();
            if (typeof cf7_logic != 'undefined')  { 
                $("input").trigger("cf7_logic");
            }
        })
        $.cf7_formulas = function(){
           var total = 0;
           var max = 100;
           var reg =[]; 
           var match;
           $("form.wpcf7-form input").each(function () { 
                if( $(this).attr("type") == "checkbox" || $(this).attr("type") == "radio"  ) {
                    var name = $(this).attr("name").replace("[]", "");
                    reg.push(name);
                }else{
                    reg.push($(this).attr("name"));
                }
           })
           $("form.wpcf7-form select").each(function () { 
                reg.push($(this).attr("name"));
           })
           reg = $.remove_duplicates_ctf7(reg);
         var field_regexp = new RegExp( '('+reg.join("|")+')');
           $( ".ctf7-total" ).each(function( index ) {
                var eq = $(this).data('formulas');
                if(eq == "") {
                    return ;
                }
               eq = eq.toString();
               eq = eq.replace(/ /g,'');
               while ( match = field_regexp.exec( eq ) ){
                    var type = $("input[name="+match[0]+"]").attr("type");
                    if( type === undefined ) {
                        var type = $("input[name='"+match[0]+"[]']").attr("type");
                    }
                    if( type =="checkbox" ){
                        var vl = 0;
                        $("input[name='"+match[0]+"[]']:checked").each(function () {
                                var row_value = $(this).val();
                                var n = row_value.search(/\|/i);
                                if(n>0){
                                    var vls = row_value.split("|");
                                    vl += new Number( vls[0] );
                                }else{
                                    vl += new Number($(this).val());
                                    var text_lb = $(this).closest("span").find(".wpcf7-list-item-label").text();
                                    if( text_lb != "" ){
                                        $(this).val(vl+ "|" +text_lb);
                                    }
                                } 
                        });
                        $("input[name='"+match[0]+"']:checked").each(function () {
                                 vl += new Number($(this).val());
                        });
                    }else if( type == "radio"){
                        var vl = $("input[name='"+match[0]+"']:checked").val();
                    }
                    else if( type == "text"){ 
                        var vl = $("input[name="+match[0]+"]").val();
                    }else if( type == "date"){ 
                        var vl = $("input[name="+match[0]+"]").val();
                    }
                    else if( type === undefined ){
                        var vl = $("select[name="+match[0]+"]").val();
                        var n = vl.search(/\|/i);
                        if(n>0){
                            var vls = vl.split("|");
                            vl = vls[0];
                        }else{
                            var text_lb = $("select[name="+match[0]+"]").find(":selected").text();
                            $("select[name="+match[0]+"]").find(":selected").val(vl+ "|" + text_lb);
                        }   
                    }else{
                        if( $("input[name="+match[0]+"]").hasClass( "ctf7-total" ) ) {
                            var vl = $("input[name="+match[0]+"]").attr("data-number");
                        }else{
                            var vl = $("input[name="+match[0]+"]").val();
                        }
                    }
                    if( $("input[name="+match[0]+"]").hasClass("number-format") ){
                        $("input[name="+match[0]+"]").autoNumeric();
                        vl = $("input[name="+match[0]+"]").autoNumeric("get");
                    }else{
                    }
                    if( vl == ""){
                        vl = 0;
                    }
                    var reg_inner = new RegExp(match[0] + "(?!\\d)","gm"); 
                    eq = eq.replace( reg_inner, vl ); 
                }

                if(cf7_calculator.pro == "ok"){
                    eq = $.cf7_fomulas_elseif(eq);
                    eq = $.cf7_fomulas_days(eq);
                    eq = $.cf7_fomulas_months(eq);
                    eq = $.cf7_fomulas_years(eq);
                    eq = $.cf7_fomulas_floor(eq);
                    eq = $.cf7_fomulas_mod(eq);
                    eq = $.cf7_fomulas_max(eq);
                    eq = $.cf7_fomulas_min(eq);
                    eq = $.cf7_fomulas_hours(eq);
                     eq = $.cf7_fomulas_floor(eq);
                    eq = $.cf7_fomulas_floor_2(eq);
                    eq = $.cf7_fomulas_round(eq);
                    eq = $.cf7_fomulas_round_2(eq);
                    eq = $.cf7_fomulas_ceil(eq);
                    eq = $.cf7_fomulas_age(eq);
                    eq = $.cf7_fomulas_age_2(eq);
                    eq = $.cf7_fomulas_avg(eq);
                    eq = $.cf7_fomulas_round_custom(eq);
                    try{
                        var r = mexp.eval( eq ); // Evaluate the final equation
                        total = r;
                    }
                    catch(e)
                    {
                        total = eq;
                    }
                }else{
                    try{
                        var r = eval( eq );
                        total = r;
                    }
                    catch(e)
                    {
                        total = eq+" Pro version";
                    }

                }
                $(this).attr("data-number",total);
                if( $(this).hasClass("number-format") ){
                    $(this).autoNumeric();
                    $(this).autoNumeric("set",total);
                    $(this).parent().find('.cf7-calculated-name').autoNumeric();
                    $(this).parent().find('.cf7-calculated-name').autoNumeric("set",total);
                }else{
                    $(this).val(total);
                    $(this).parent().find('.cf7-calculated-name').html(total);
                }
           });
        }
    $.remove_duplicates_ctf7 = function(arr) {
        var obj = {};
        var ret_arr = [];
        for (var i = 0; i < arr.length; i++) {
            obj[arr[i]] = true;
        }
        for (var key in obj) {
            if("_wpcf7" == key || "_wpcf7_version" == key  || "_wpcf7_locale" == key  || "_wpcf7_unit_tag" == key || "_wpnonce" == key || "undefined" == key  || "_wpcf7_container_post" == key || "_wpcf7_nonce" == key  ){
            }else {
                if(key !=""){
                    ret_arr.push(key +"(?!\\d)");
                }
            }
        }
        return ret_arr;
    }
    $.cf7_fomulas_round = function(x){ 
            var re = /round\(([^()]*)\)/gm;
            x = x.replace( re,function (x) {
                    x = x.replace(/[round()]/g, '');
                    x = mexp.eval(x);
                     return Math.round(x);
                });
            if( x.match(re) ){
                x = $.cf7_fomulas_round(x);
            }
            return x;
        }
        $.cf7_fomulas_avg = function(x){ 
            var re = /avg\(([^()]*)\)/gm;
            x = x.replace( re,function (x) {
                    x = x.replace(/[agv()]/g, '');
                    var elmt = x.split(",");
                   var sum = 0;
                    for( var i = 0; i < elmt.length; i++ ){
                        sum += parseInt( elmt[i], 10 ); //don't forget to add the base
                    }
                     return sum/elmt.length;
                });
            if( x.match(re) ){
                x = $.cf7_fomulas_avg(x);
            }
            return x;
        }
        $.cf7_fomulas_round_2 = function(x){ 
            var re = /round2\(([^()]*)\)/gm;
            x = x.replace( re,function (x) {
                    x = x.replace(/[round2()]/g, '');
                    x = mexp.eval(x);
                     return Math.round(x * 100) / 100
                });
            if( x.match(re) ){
                x = $.cf7_fomulas_round_2(x);
            }
            return x;
        }
        $.cf7_fomulas_floor = function(x){ 
            var re = /floor\(([^()]*)\)/gm;
            x = x.replace( re,function (x) {
                    x = x.replace(/[floor()]/g, '');
                    x = mexp.eval(x);
                     return Math.floor(x);
                });
            if( x.match(re) ){
                x = $.cf7_fomulas_floor(x);
            }
            return x;
        }
        $.cf7_fomulas_floor_2 = function(x){ 
            var re = /floor2\(([^()]*)\)/gm;
            x = x.replace( re,function (x) {
                    x = x.replace(/[floor2()]/g, '');
                    x = mexp.eval(x);
                     return Math.floor(x * 100) / 100
                });
            if( x.match(re) ){
                x = $.cf7_fomulas_floor_2(x);
            }
            return x;
        }
        $.cf7_fomulas_ceil = function(x){ 
            var re = /ceil\(([^()]*)\)/gm;
            x = x.replace( re,function (x) {
                    x = x.replace(/[ceil()]/g, '');
                    x = mexp.eval(x);
                     return Math.ceil(x);
                });
            if( x.match(re) ){
                x = $.cf7_fomulas_ceil(x);
            }
            return x;
        }
        $.cf7_fomulas_mod = function(x){ 
            var re = /mod\(([^()]*)\)/gm;
            x = x.replace( re,function (x) {
                    x = x.replace(/[mod()]/g, '');
                    var datas = x.split(",");
                     return  datas[0] % datas[1];
                });
            if( x.match(re) ){
                x = $.cf7_fomulas_mod(x);
            }
            return x;
        }
        $.cf7_fomulas_elseif = function(x){ 
            var re = /if\(([^()]*)\)/gm;
            x = x.replace( re,function (x) {
                    return $.cf7_fomulas_if(x);
                });
            if( x.match(re) ){
                x = $.cf7_fomulas_elseif(x);
            }
            return x;
        }
        $.cf7_fomulas_if = function(x){
            x = x.replace(/[if()]/g, '');
            var data = x.split(",");
            try {
                  if(eval(data[0])){
                      return mexp.eval(data[1]);
                  }else{
                      return mexp.eval(data[2]);
                  }
            } catch (e) {
               return 0;
            }               
        }
        $.cf7_fomulas_age = function(x){ 
            var re = /age\(([^()]*)\)/gm;
            x = x.replace( re,function (x) {
                    x = x.replace(/[age()]/g, '');
                    var dob = new Date(x);
                    var today = new Date();
                    return Math.floor((today-dob) / (365.25 * 24 * 60 * 60 * 1000));
                });
            if( x.match(re) ){
                x = $.cf7_fomulas_age(x);
            }
            return x;
        }
        $.cf7_fomulas_age_2 = function(x){ 
            var re = /age2\(([^()]*)\)/gm;
            x = x.replace( re,function (x) {
                    x = x.replace(/[age2()]/g, '');
                    var datas = x.split(",");
                    var dob = new Date(datas[0]);
                    var today = new Date(datas[1]);
                    return Math.floor((today-dob) / (365.25 * 24 * 60 * 60 * 1000));
                });
            if( x.match(re) ){
                x = $.cf7_fomulas_age_2(x);
            }
            return x;
        }
        $.cf7_fomulas_days = function(x){ 
            var re = /days\(([^()]*)\)/gm;
            x = x.replace( re,function (x) {
                     x = x.replace(/[days()]/g, '');
                     var datas = x.split(",");

                     if( datas[1] == "now" ){
                        var today = new Date();
                        var day_end1 = today.getFullYear()+'-'+(today.getMonth()+1)+'-'+today.getDate();
                     }else{
                        var day_end1= datas[1];
                     }
                     if( datas[0] == "now" ){
                        var today = new Date();
                        var day_start1 = today.getFullYear()+'-'+(today.getMonth()+1)+'-'+today.getDate();
                     }else{
                        var day_start1 = datas[0];
                     }
                     var day_end = $.cf7_fomulas_parse_date(day_end1);
                     var day_start = $.cf7_fomulas_parse_date(day_start1);
                      if( isNaN(day_end) || isNaN(day_start) ){
                        return 0;
                      }else{
                        return $.cf7_fomulas_datediff(day_end,day_start);
                      }
                });
            if( x.match(re) ){
                x = $.cf7_fomulas_days(x);
            }
            return x;
        }
        $.cf7_fomulas_months = function(x){ 
            var re = /months\(([^()]*)\)/gm;
            x = x.replace( re,function (x) {
                     x = x.replace(/[months()]/g, '');
                     var datas = x.split(",");
                     if( datas[1] == "now" ){
                        var today = new Date();
                        var day_end1 = today.getFullYear()+'-'+(today.getMonth()+1)+'-'+today.getDate();
                     }else{
                        var day_end1= datas[1];
                     }
                     var day_end = $.cf7_fomulas_parse_date(day_end1);
                     if( datas[0] == "now" ){
                        var today = new Date();
                        var day_start1 = today.getFullYear()+'-'+(today.getMonth()+1)+'-'+today.getDate();
                     }else{
                        var day_start1 = datas[0];
                     }
                     var day_start = $.cf7_fomulas_parse_date(day_start1);
                      if( isNaN(day_end) || isNaN(day_start) ){
                        return 0;
                      }else{
                        return day_start.getMonth() - day_end.getMonth() +  (12 * (day_start.getFullYear() - day_end.getFullYear()))
                      }
                });
            if( x.match(re) ){
                x = $.cf7_fomulas_months(x);
            }
            return x;
        }
        $.cf7_fomulas_years = function(x){ 
            var re = /years\(([^()]*)\)/gm;
            x = x.replace( re,function (x) {
                     x = x.replace(/[years()]/g, '');
                     var datas = x.split(",");
                     if( datas[1] == "now" ){
                        var today = new Date();
                        var day_end1 = today.getFullYear()+'-'+(today.getMonth()+1)+'-'+today.getDate();
                     }else{
                        var day_end1= datas[1];
                     }
                     var day_end = $.cf7_fomulas_parse_date(day_end1);
                     if( datas[0] == "now" ){
                        var today = new Date();
                        var day_start1 = today.getFullYear()+'-'+(today.getMonth()+1)+'-'+today.getDate();
                     }else{
                        var day_start1 = datas[0];
                     }
                     var day_start = $.cf7_fomulas_parse_date(day_start1);
                      if( isNaN(day_end) || isNaN(day_start) ){
                        return 0;
                      }else{
                        return day_start.getFullYear() - day_end.getFullYear();
                      }
                });
            if( x.match(re) ){
                x = $.cf7_fomulas_years(x);
            }
            return x;
        }
        $.cf7_fomulas_floor = function(x){ 
            var re = /floor\(([^()]*)\)/gm;
            x = x.replace( re,function (x) {
                    x = x.replace(/[floor()]/g, '');
                    x = mexp.eval(x);
                     return Math.floor(x);
                });
            if( x.match(re) ){
                x = $.cf7_fomulas_floor(x);
            }
            return x;
        }
        $.cf7_fomulas_round_custom = function(x){ 
            var re = /custom\(([^()]*)\)/gm;
            x = x.replace( re,function (x) {
                    x = x.replace(/[custom()]/g, '');
                    x = mexp.eval(x);
                    x = x.toString();
                    var values = x.split(".");
                    var qk_c = values[0];
                    if( values.length > 1 ){
                        var qk_l =  values[1].substring(0,1);;
                        if( qk_l != 0 ){
                           if( qk_l < 6 ){
                                qk_l = 5;
                           }else{
                                qk_l = 0;
                                qk_c++;
                           }
                        }
                        var kq= qk_c+"."+qk_l;
                        return kq;
                    }else{
                        return x;
                    }
                });
            if( x.match(re) ){
                x = $.cf7_fomulas_round_custom(x);
            }
            return x;
        }
        $.cf7_fomulas_mod = function(x){ 
            var re = /mod\(([^()]*)\)/gm;
            x = x.replace( re,function (x) {
                    x = x.replace(/[mod()]/g, '');
                    var datas = x.split(",");
                     return  datas[0] % datas[1];
                });
            if( x.match(re) ){
                x = $.cf7_fomulas_floor(x);
            }
            return x;
        }
         $.cf7_fomulas_max = function(x){ 
            var re = /max\(([^()]*)\)/gm;
            x = x.replace( re,function (x) {
                    x = x.replace(/[max()]/g, '');
                    var datas = x.split(",");
                     datas = datas.map(element => {
                          return element.trim();
                        });
                     return Math.max.apply(null,datas);
                });
            if( x.match(re) ){
                x = $.cf7_fomulas_max(x);
            }
            return x;
        }
        $.cf7_fomulas_min = function(x){ 
            var re = /min\(([^()]*)\)/gm;
            x = x.replace( re,function (x) {
                    x = x.replace(/[min()]/g, '');
                    var datas = x.split(",");
                      datas = datas.map(element => {
                          return element.trim();
                        });
                     return Math.min.apply(null,datas);
                });
            if( x.match(re) ){
                x = $.cf7_fomulas_min(x);
            }
            return x;
        }
        $.cf7_fomulas_hours = function(x){ 
            var re = /hours\(([^()]*)\)/gm;
            x = x.replace( re,function (x) {
                    x = x.replace(/[hours()]/g, '');
                    var datas = x.split(",");
                    var hour_start = datas[1];
                    var hour_end = datas[0];
                    var hour_start_m =  hour_start.split(":");
                    var hour_end_m =  hour_end.split(":");
                    hour_start_m = parseInt(hour_start_m[0]);
                    hour_end_m = parseInt(hour_end_m[0]);
                    if( hour_start_m >= 22 && hour_end_m <= 7 ){
                        var ok = -1;
                    }else{
                       var ok= $.cf7_fomulas_hoursiff(hour_start,hour_end); 
                    }
                   return ok;
                });
            if( x.match(re) ){
                x = $.cf7_fomulas_hours(x);
            }
            return x;
        }
        $.cf7_fomulas_parse_date = function(str){
            return new Date(str);
        }
        $.cf7_cover_date_format = function(str,id){
            var date="";
            var format = id.data("date-format");
            if( format == "m/d/Y" ) {
                var datas = str.split("/");
                date = datas[2] + "-" + datas[0] + "-" + datas[1];
            } else if( format == "d/m/Y") {
                var datas = str.split("/");
                date = datas[2] + "-" + datas[1] + "-" + datas[0];
            } else if( format == "F j, Y"){
                date = str;
            }
            return date;
        }
        $.cf7_fomulas_datediff = function(first, second){
            second =  second.getTime();
            first =  first.getTime();
            return Math.round((second-first)/(1000*60*60*24));
        }
        if ( $( ".wpcf7-form" ).length ) {
            $.cf7_formulas();
            $(".cf7-hide").closest('p').css('display', 'none');
        }
    })
})(jQuery);
