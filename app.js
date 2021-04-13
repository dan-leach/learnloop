var data = {
  fetchID: '',
  certName: '',
  sDetails: {
    sIdent: '',
    fName: '',
    fEmail: '',
    sName: '',
    sDate: '',
    sCert: false
  },
  feedback: {
    positiveComments: '',
    constructiveComments: '',
    overallScore: null
  },
  show: {
    loader: true,
    welcome: false,
    giveFeedback: false,
    completedFeedback: false,
    createSession: false,
    requestFeedback: false,
    certificateOptions: false
  },
  invite: {
    full: '',
    link: '',
    qr: ''
  },
  showCopyBtns: true,
  quote: '',
  quoteList: [
        {
            quote: "We all need people who will give us feedback. That’s how we improve.",
            cite: "Bill Gates"
        },
        {
            quote: "Criticism, like rain, should be gentle enough to nourish a man’s growth without destroying his roots.",
            cite: "Frank A. Clark"
        },
        {
            quote: "Feedback is the breakfast of champions.",
            cite: "Ken Blanchard"
        },
        {
            quote: "Feedback is a gift. Ideas are the currency of our next success. Let people see you value both feedback and ideas.",
            cite: "Jim Trinka and Les Wallace"
        }
    ]
}

var app = new Vue({
    el: '#app',
    data: data,
    methods: {
      getSession: function(){
        console.log("Start getSession...")
        try {
          app.closeAlert()
        } catch (e) {
          //alert not open
        }
        $( "#getExistingSession" ).html("<span class='spinner-border spinner-border-sm'></span>  Please wait...")
        $( "#getExistingSession" ).prop( "disabled", true );
        if (data.fetchID == ''){
          //do nothing
        } else {
          var xhttp = new XMLHttpRequest()
          xhttp.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
              console.log (this.responseText)
              try {
                var obj = JSON.parse(this.responseText)
                data.sDetails.sIdent = data.fetchID
                data.sDetails.sName = obj[0].sName
                data.sDetails.sDate = obj[0].sDate
                data.sDetails.fName = obj[0].fName
                if (obj[0].sCert == true) {
                    data.sDetails.sCert = true
                } else {
                    data.sDetails.sCert = false
                }
                app.hideComponent('all')
                app.showComponent('giveFeedback')
              } catch (e) {
                app.hideComponent('all')
                app.showComponent('welcome')
                $( "#getExistingSession" ).html("Give feedback")
                $( "#getExistingSession" ).prop( "disabled", false );
                app.showAlert(this.responseText)
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
          app.closeAlert()
          $( "#submitCreateSession" ).html("<span class='spinner-border spinner-border-sm'></span>  Please wait...")
          $( "#submitCreateSession" ).prop( "disabled", true );
        var xhttp = new XMLHttpRequest()
        xhttp.onreadystatechange = function() {
          if (this.readyState == 4 && this.status == 200) {
            console.log (this.responseText)
            try {
              var obj = JSON.parse(this.responseText)
              data.sDetails.sIdent = obj.uuid
              var tempLink = 'https://feedback.danleach.uk/?' + data.sDetails.sIdent
              data.invite.full = "Dear attendee,<br><br>You attended my session <strong>'" + data.sDetails.sName + "'</strong> on " + data.sDetails.sDate + ". I would be grateful if you would take the time to provide some anonymous feedback for me using the link below.<br><br><h4><a href='" + tempLink + "'>Please click here to provide your feedback</a></h4>Or alternatively visit <strong><a href='https://feedback.danleach.uk'>feedback.danleach.uk</a></strong> and enter the session ID code: <strong>" + data.sDetails.sIdent + "</strong><br><br>Many thanks,<br>" + data.sDetails.fName + "<br><br><i>You can request feedback for your own sessions too using the feedback tool. Visit <a href='https://feedback.danleach.uk'>feedback.danleach.uk</a> to get started!</i>"
              data.invite.link = '<a href="' + tempLink + '">' + tempLink + '</a>'
              data.invite.qr = '<img src="https://chart.googleapis.com/chart?chs=250x250&cht=qr&chl=' + tempLink + '&choe=UTF-8" alt="QR code" height="250">'
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
        xhttp.send("fName="+data.sDetails.fName+"&fEmail="+data.sDetails.fEmail+"&sName="+data.sDetails.sName+"&sDate="+data.sDetails.sDate+"&sCert="+data.sDetails.sCert);
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
              console.log (this.responseText)
              try {
                var obj = JSON.parse(this.responseText)
                app.hideComponent('all')
                app.showComponent('completedFeedback')
              } catch (e) {
                app.showAlert(this.responseText)
                $( "#submitGiveFeedback" ).html("Retry give feedback?")
                $( "#submitGiveFeedback" ).prop( "disabled", false );
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
      showNotice: function(msg){
        $( "#alert" ).html("<div class='alert alert-success alert-dismissible fade show'><button type='button' class='close' data-dismiss='alert'>&times;</button>"+msg+"</div><br>");
      },
      closeAlert: function(){
        $( "#alert" ).html("");
      },
      copy: function(copyItem){
        console.log('Start copy('+copyItem+')')
        // Query the elements
        const codeEle = document.getElementById(copyItem);
        const selection = window.getSelection();

        // Save the current selection
        const currentRange = selection.rangeCount === 0
            ? null : selection.getRangeAt(0);

        // Select the text content of code element
        const range = document.createRange();
        range.selectNodeContents(codeEle);
        selection.removeAllRanges();
        selection.addRange(range);

        // Copy to the clipboard
        try {
            document.execCommand('copy');
            if (this.isIE()){
              this.showAlert('Copy button may not work correctly in internet explorer. Use Ctrl+C instead.')
            } else {
              this.showNotice('Copied to clipboard.')
            }
        } catch (err) {
            // Unable to copy
            this.showAlert('Error copying to clipboard.')
        } finally {
            // Restore the previous selection
            selection.removeAllRanges();
            currentRange && selection.addRange(currentRange);
        }
      },
      isIE: function() {
        const ua = window.navigator.userAgent; //Check the userAgent property of the window.navigator object
        const msie = ua.indexOf('MSIE '); // IE 10 or older
        const trident = ua.indexOf('Trident/'); //IE 11
      
        return (msie > 0 || trident > 0);
      },
      selectText: function(id){
        var sel, range;
        var el = document.getElementById(id); //get element id
        if (window.getSelection && document.createRange) { //Browser compatibility
          sel = window.getSelection();
          if(sel.toString() == ''){ //no text selection
           window.setTimeout(function(){
            range = document.createRange(); //range object
            range.selectNodeContents(el); //sets Range
            sel.removeAllRanges(); //remove all ranges from selection
            sel.addRange(range);//add Range to a Selection.
          },1);
          }
        }else if (document.selection) { //older ie
          sel = document.selection.createRange();
          if(sel.text == ''){ //no text selection
            range = document.body.createTextRange();//Creates TextRange object
            range.moveToElementText(el);//sets Range
            range.select(); //make selection.
          }
        }
      },
      logger: function(msg){
          //for debugging on mobiles
         var log = $( "#logger" ).html()
         $( "#logger" ).html(log + "<br>" + msg)
      },
      showQuote: function(){
        const random = Math.floor(Math.random() * data.quoteList.length);
        data.quote = "<q>" + data.quoteList[random].quote + "</q><br><i>– " + data.quoteList[random].cite + "</i>"
      },
      showCertificateOptions: function(){
        data.show.certificateOptions = true
        $( "#showCertificateOptions" ).html("Toggle")
        data.sDetails.sCert = !data.sDetails.sCert
        console.log('sDetails.sCert: ' + data.sDetails.sCert)
      },
      downloadCertificate: function(){
        $( "#getCertificateForm" ).addClass("was-validated")
        if (data.certName != ''){
          app.closeAlert()
          document.getElementById("getCertificateForm").submit();
        } else {
            app.showAlert("Please correct the problems with the form before downloading your certificate.")
        }
      }
    },
    mounted: function() {
      if (this.isIE()) {
        data.showCopyBtns = false
      }
      data.fetchID = window.location.search.replace("?","")
      if (data.fetchID){
        console.log("Autoload for session: " + data.fetchID)
        window.history.replaceState("https://danleach.uk/feedbackv2/", "Feedback Tool", "/feedbackv2/")
        this.getSession()
      } else {
        this.hideComponent('all')
        this.showComponent('welcome')
      }
      this.showQuote()
    }
  })
