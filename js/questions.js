function submit_form()
{
  $('input[type=checkbox]:visible, input[type=radio]:visible').closest('.question').find('.question_title').removeClass('nok');

  var nok = $('input[type=checkbox]:visible, input[type=radio]:visible').parent().map(function() {
    var ok = $(this).find('input:checked').length > 0;
    return ok? null : this;
  });

  nok.push.apply(nok, $('select:visible').map(function() {
    var ok =  $(this).find('option:selected').val();
    return ok? null : this;
  }).closest('.question').find('.question_title'));
  
  if(nok.length > 0)
  {
    nok.closest('.question').find('.question_title').addClass('nok');

    $("html, body").animate({ scrollTop: 0 }, "slow").promise().done(function() {
      //alert("Non hai risposto a tutte le domande.");
      $('#myModal').reveal({animation: 'fadeAndPop',                   //fade, fadeAndPop, none
         animationspeed: 200,                       //how fast animtions are
         closeonbackgroundclick: true,              //if you click background will modal close?
         dismissmodalclass: 'close-reveal-modal'    //the class of a button or element that will close an open modal);
      });
    });

  }
  else
  {
    var conf = confirm("Sei sicuro di volerlo inviare? Non potrai piu' cambiare le tue risposte.")
    if(conf)
      $("#hidden_submit").click();
  }
}


function fetch_questions()
{
    var s = $('[data-side-effects=true]:checked,'
             +'[data-side-effects=true] option:selected').map(function() {
        return this.value? this.value : null; //empty values don't count (the default one)
    }).get().join();

    $.get(
        "fetch_questions_list.php",
        {q: s},
        function(data) {
           var questions_to_show = JSON.parse(data);
           $('.question').each(function() {
              var to_show = $.inArray($(this).attr('data-question_id'), questions_to_show) >= 0;
              var visible = $(this).is(":visible");

              if(to_show && (!visible))
              {
                //$(this).find('input[type=radio], select').attr('required', true);
                $(this).slideDown("slow");
              }
              else if((!to_show) && visible)
              {
                //$(this).find('input[type=radio], select').attr('required', false);
                $(this).slideUp("slow");
              }
            });

           $('body').css('cursor', 'auto');
        }
    );

    $('body').css('cursor', 'progress');
}


$(document).ready(function() {
    //when the page has been loaded, add onclick/change handlers
    $('[data-side-effects=true]').change(fetch_questions);
    $(".question").hide();
    fetch_questions(); //fetch_questions at the beginning

    $("#submit_button").click(submit_form);

    $('input, select').change(function() { //remove nok when selecting something
      $(this).closest('.question').find('.nok').removeClass('nok');
    })
});