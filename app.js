var data = {
  fetchID: '',
  sDetails: {
    sIdent: '',
    fName: '',
    fEmail: '',
    sName: '',
    sDate: ''
  },
  feedback: {
    positiveComments: '',
    constructiveComments: '',
    overallScore: null
  },
  show: {
    loader: true,
    getExisting: false,
    welcome: false,
    giveFeedback: false,
    completedFeedback: false,
    createSession: false,
    requestFeedback: false,
  },
  createSessionResponse: '',
  getSessionResponse: '',
  giveFeedbackResponse: ''
}

var app = new Vue({
    el: '#app',
    data: data,
    methods: {
      getSession: function(){
        console.log("Start getSession...")
        if (data.fetchID == ''){
          //do nothing
        } else {
          var xhttp = new XMLHttpRequest()
          xhttp.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
              data.getSessionResponse = this.responseText
              try {
                var obj = JSON.parse(this.responseText)
                data.sDetails.sIdent = data.fetchID
                data.sDetails.sName = obj[0].sName
                data.sDetails.sDate = obj[0].sDate
                data.sDetails.fName = obj[0].fName
                app.hideComponent('all')
                app.showComponent('giveFeedback')
              } catch (e) {
                app.showAlert(this.responseText)
                app.hideComponent('all')
                app.showComponent('welcome')
                app.showComponent('getExisting')
              }
            }
          };
          xhttp.open("GET", "api/getSession.php?fetchID="+data.fetchID, true)
          xhttp.send()
        }
      },
      createSession: function(){
        console.log("Start createSession...")
        $( "#createSessionForm" ).addClass("was-validated")
        if (data.sDetails.sName != '' && data.sDetails.sDate != '' && data.sDetails.fName != '' && data.sDetails.fEmail != ''){
          //need to check email is actually email format, not just not blank
          //need to check sDate is appropriate (is date)
          app.closeAlert()
          $( "#submitCreateSession" ).html("<span class='spinner-border spinner-border-sm'></span>  Please wait...")
          $( "#submitCreateSession" ).prop( "disabled", true );
        var xhttp = new XMLHttpRequest()
        xhttp.onreadystatechange = function() {
          if (this.readyState == 4 && this.status == 200) {
            data.createSessionResponse = this.responseText
            console.log (data.createSessionResponse)
            try {
              var obj = JSON.parse(this.responseText)
              data.sDetails.sIdent = obj.uuid
              app.hideComponent('all')
              app.showComponent('requestFeedback')
            } catch (e) {
              app.showAlert(this.responseText)
              $( "#submitCreateSession" ).html("Retry create session?")
              $( "#submitCreateSession" ).prop( "disabled", false );
            }
          }
        };
        xhttp.open("POST", "api/createSession.php", true);
        xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        xhttp.send("fName="+data.sDetails.fName+"&fEmail="+data.sDetails.fEmail+"&sName="+data.sDetails.sName+"&sDate="+data.sDetails.sDate);
      } else {
        app.showAlert("Please correct the problems with the form before submitting your feedback.")
      }
      },
      giveFeedback: function(){
        console.log("Start giveFeedback...")
        $( "#giveFeedbackForm" ).addClass("was-validated")
        if (data.feedback.positiveComments != '' && data.feedback.constructiveComments != '' & data.feedback.overallScore != null){
          app.closeAlert()
          $( "#submitGiveFeedback" ).html("<span class='spinner-border spinner-border-sm'></span>  Please wait...")
          $( "#submitGiveFeedback" ).prop( "disabled", true );
          var xhttp = new XMLHttpRequest()
          xhttp.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
              data.createSessionResponse = this.responseText
              console.log (data.createSessionResponse)
              try {
                var obj = JSON.parse(this.responseText)
                app.hideComponent('all')
                app.showComponent('completedFeedback')
              } catch (e) {
                app.showAlert(this.responseText)
              }
            }
          };
          xhttp.open("POST", "api/giveFeedback.php", true);
          xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
          xhttp.send("sIdent="+data.sDetails.sIdent+"&positiveComments="+data.feedback.positiveComments+"&constructiveComments="+data.feedback.constructiveComments+"&overallScore="+data.feedback.overallScore);
        } else {
          app.showAlert("Please correct the problems with the form before submitting your feedback.")
        }
      },
      showComponent: function(comp){
        console.log('showComponent: ' + comp)
        data.show[comp] = true
      },
      hideComponent: function(comp){
        console.log('hideComponent: ' + comp)
        if (comp == 'all'){
          var x
          for (x in data.show){
            data.show[x] = false
          }
        } else {
          data.show[comp] = false
        }
      },
      showAlert: function(msg){
        $( "#alert" ).html("<div class='alert alert-danger alert-dismissible fade show'><button type='button' class='close' data-dismiss='alert'>&times;</button>"+msg+"</div><br>");
      },
      closeAlert: function(){
        $( "#alert" ).html("");
      },
    },
    mounted: function() {
      data.fetchID = window.location.search.replace("?","")
      if (data.fetchID){
        console.log("Autoload for session: " + data.fetchID)
        this.getSession()
      } else {
        this.hideComponent('loader')
        this.showComponent('welcome')
        this.showComponent('getExisting')
      }
    }
  })