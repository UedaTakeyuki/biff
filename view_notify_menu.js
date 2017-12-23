var app = new Vue(
  {
    el: "#view-notify-menu",
    data: {
    },
    methods: {
      sendnotification: function(to,now,filename){
        $.ajax({
          type: "POST",
          url: gIni.sendscript_url,
          data: {
            to: to,
            now: now,
            filename: filename,
          },
          dataType: "json",
        })
      }
    }
  }
)

