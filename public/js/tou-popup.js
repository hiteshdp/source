
function touInstall(formToValidate, rules) {
  let $formToValidate = $(formToValidate);

  let options = {
    errorPlacement: function (error, element) {
      if (element.attr('name') === 'data_policy')
        error.insertAfter('.dp_error_container');
      else
        error.insertAfter(element);
    }
  };

  if (rules) {
    options.rules = rules;
  }

  $formToValidate.validate(options);
}

$(document).ready(function () {
  
  /* Ruls for redumption-page-form form validation */
  $("#redumption-page-form").validate({  
    rules: {
        email_confirmation : {
            equalTo : "#email"
        },
    },

  }) 

   /* Ruls for lockout-page-form form validation */
   $("#lockout-page-form").validate({  
    rules: {
        email_confirmation : {
            equalTo : "#email"
        },
    },

  }) 

  /*Redumption pop up - start*/
  $('.js-modal-open-redumption').click(function () {
      
    // Set from id
    $('#formSubmit').attr('form','redumption-page-form')
    
    let $this = $(this);
    var isValid = $("#redumption-page-form").valid();
    // let $formToValidate = $($this.data('validate'));

    if (isValid) {
      $this.magnificPopup({
        items: {
          src: $this.data('open')
        },
        type: 'inline'
      });

      $.magnificPopup.open({
        items: {
          src: $this.data('open')
        },
        type: 'inline'
      });
    }
  });
/*Redumption pop up - end*/

/*Lockout pop up - start*/

  $('.js-modal-open-lockout').click(function () {
      

    // Set from id
    $('#formSubmit').attr('form','lockout-page-form')
    
    
    let $this = $(this);
    // let $formToValidate = $($this.data('validate'));
    var isValid = $("#lockout-page-form").valid();
    
    if (isValid) {
      $this.magnificPopup({
        items: {
          src: $this.data('open')
        },
        type: 'inline'
      });

      $.magnificPopup.open({
        items: {
          src: $this.data('open')
        },
        type: 'inline'
      });
    }
  });
/*Lockout pop up - end*/

/*Promotion pop up - start*/
$('.js-modal-open-promotion').click(function () {

    // Set from id
    $('#formSubmit').attr('form','offer-promotion-form')
    
    let $this = $(this);
    // let $formToValidate = $($this.data('validate'));
    var isValid = $("#offer-promotion-form").valid();
    
    if (isValid) {
      $this.magnificPopup({
        items: {
          src: $this.data('open')
        },
        type: 'inline'
      });

      $.magnificPopup.open({
        items: {
          src: $this.data('open')
        },
        type: 'inline'
      });
    }
  });
/*Promotion pop up - end*/

  $('.touSubmit').click(function () {
   
    let $this = $(this);
    $.magnificPopup.close();
    $('#' + $this.attr('form')).submit();
  });

  $('#tou-modal .modal-content').scroll(function () {
    let scrolledTop = this.scrollHeight - this.offsetHeight;

    let button = document.querySelector('#formSubmit');

    if (Math.floor(this.scrollTop / scrolledTop * 100) >= 75) {
      button.removeAttribute('disabled');
      button.style.backgroundColor = '#0db2af';
    } else {
      // console.log($button, 'disable');
      button.setAttribute('disabled', true);
      button.style.backgroundColor = '#000';
    }
  });

   // For show hide Other input
   $("input[name='type']").change(function(){
    if($(this).val()=="Other")
    {
        $("#otherTextBox").show();
    }
    else
    {
        $("#otherTextBox").hide();
    }
  });

});
