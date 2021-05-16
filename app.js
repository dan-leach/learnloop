var data = {
  fetchID: '',
  certName: '',
  sDetails: {
    sIdent: '',
    fName: '',
    fEmail: '',
    sName: '',
    sDate: '',
    sCert: true
  },
  feedback: {
    positiveComments: '',
    constructiveComments: '',
    overallScore: null,
    scoreText: 'Please indicate an overall score using the slider.'
  },
  show: {
    loader: true,
    welcome: false,
    giveFeedback: false,
    completedFeedback: false,
    createSession: false,
    requestFeedback: false,
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
        },
        {
          quote: "There is no failure. Only feedback.",
          cite: "Robert Allen"
        },
        {
          quote: "No matter how good you think you are as a leader, my goodness, the people around you will have all kinds of ideas for how you can get better. So for me, the most fundamental thing about leadership is to have the humility to continue to get feedback and to try to get better – because your job is to try to help everybody else get better.",
          cite: "Jim Yong Kim"
        },
        {
          quote: "True intuitive expertise is learned from prolonged experience with good feedback on mistakes.",
          cite: "Daniel Kahneman"
        },
        {
          quote: "Criticism may not be agreeable, but it is necessary. It fulfils the same function as pain in the human body. It calls attention to an unhealthy state of things.",
          cite: "Winston Churchill"
        },
        {
          quote: "All that is valuable in human society depends upon the opportunity for development accorded the individual.",
          cite: "Albert Einstein"
        },
        {
          quote: "Strive for continuous improvement, instead of perfection.",
          cite: "Kim Collins"
        },
        {
          quote: "Never be afraid to fail. Failure is only a stepping stone to improvement.",
          cite: "Tony Jaa"
        },
        {
          quote: "There is always space for improvement, no matter how long you’ve been in the business.",
          cite: "Oscar De La Hoya"
        },
        {
          quote: "Knowledge rests not upon truth alone, but upon error also.",
          cite: "Carl Jung"
        },
        {
          quote: "To become more effective and fulfilled at work, people need a keen understanding of their impact on others and the extent to which they’re achieving their goals in their working relationships. Direct feedback is the most efficient way for them to gather this information and learn from it.",
          cite: "Ed Batista"
        },
        {
          quote: "We can’t just sit back and wait for feedback to be offered, particularly when we’re in a leadership role. If we want feedback to take root in the culture, we need to explicitly ask for it.",
          cite: "Ed Batista"
        },
        {
          quote: "Average players want to be left alone. Good players want to be coached. Great players want to be told the truth.",
          cite: "Doc Rivers"
        },
        {
          quote: "I think it’s very important to have a feedback loop, where you’re constantly thinking about what you’ve done and how you could be doing it better.",
          cite: "Elon Musk"
        },
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
              data.invite.qr = '<div id="loaderQR" class="spinner-border" style="width: 15rem; height: 15rem;"></div><img src="https://chart.googleapis.com/chart?chs=250x250&cht=qr&chl=' + tempLink + '&choe=UTF-8" alt="QR code" id="imgQR" height="250" onload="app.showQR()" hidden>'
              app.hideComponent('all')
              app.showComponent('requestFeedback')

            } catch (e) {
              app.showAlert(this.responseText)
              $( "#submitCreateSession" ).html("Retry create session?")
              $( "#submitCreateSession" ).prop( "disabled", false )
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
          xhttp.send("sIdent="+data.sDetails.sIdent+"&positiveComments="+data.feedback.positiveComments+"&constructiveComments="+data.feedback.constructiveComments+"&overallScore="+data.feedback.overallScore+"&scoreText="+data.feedback.scoreText);
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
              this.showAlert('Copy button may not work correctly depending on your browser security settings. Use Ctrl+C instead.')
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
        data.quote = "<q>" + data.quoteList[random].quote + "</q><br><i>– " + data.quoteList[random].cite + "</i><br><a onclick='app.showQuote()'><i id='reload' class='fas fa-redo'></i></a>"
      },
      toggleCertificate: function(){
        data.sDetails.sCert = !data.sDetails.sCert
        console.log('sDetails.sCert: ' + data.sDetails.sCert)
        if (data.sDetails.sCert) {
          $( "#toggleCertificate" ).html("Disable certificate")
        } else {
          $( "#toggleCertificate" ).html("Enable certificate")
        }
        
      },
      downloadCertificate: function(){
        $( "#getCertificateForm" ).addClass("was-validated")
        if (data.certName != ''){
          app.closeAlert()
          document.getElementById("getCertificateForm").submit();
        } else {
            app.showAlert("Please correct the problems with the form before downloading your certificate.")
        }
      },
      showQR: function(){
        console.log('showQR start...')
        document.getElementById('loaderQR').setAttribute('hidden', true)
        document.getElementById('imgQR').removeAttribute('hidden')
      },
      scoreChange: function(){
        var x = document.getElementById('overallScoreRange').value
        var y = 'slider error'
        if (x>95){
          y = 'an overwhelmingly excellent session, couldn\'t be improved'
        } else if (x>80) {
          y = 'an excellent sesssion, minimal grounds for improvement'
        } else if (x>70) {
          y = 'a very good session, minor points for improvement'
        } else if (x>60) {
          y = 'a fairly good session, could be improved further'
        } else if (x>40) {
          y = 'basically sound, but needs further development'
        } else if (x>20) {
          y = 'not adequate in its current state'
        } else if (x < 20) {
          y = 'an extremely poor session'
        }
        data.feedback.scoreText = y
      },
    },
    mounted: function() {
      data.fetchID = window.location.search.replace("?","")
      if (data.fetchID){
        console.log("Autoload for session: " + data.fetchID)
        window.history.replaceState("https://feedback.danleach.uk", "Feedback Tool", "/")
        this.getSession()
      } else {
        this.hideComponent('all')
        this.showComponent('welcome')
      }
      this.showQuote()
    }
  })

//https://tobiasahlin.com/moving-letters/#10
// Wrap every letter in a span
var textWrapper = document.querySelector('.ml10 .letters');
textWrapper.innerHTML = textWrapper.textContent.replace(/\S/g, "<span class='letter'>$&</span>");

anime.timeline({loop: false})
  .add({
    targets: '.ml10 .letter',
    rotateY: [-90, 0],
    duration: 2500,
    delay: function(el, i) { return 45 * i}
  });