console.log("loader-v1.js running... to do")

var app = new Vue({
    el: '#app',
    data() {
      return {
        show: {
            welcome: false,
            loader: true
        }
      }
    },
    methods: {
      showComponent: function(comp){
        console.log('Showing: [' + comp + ']')
        this.show[comp] = true
        window.scrollTo({top:0, left:0, behavior: 'smooth'})
        if (comp == "createSession") window.history.replaceState("https://learnloop.co.uk", "LearnLoop", "/?createSession")
        if (comp == "createSessionSeries") window.history.replaceState("https://learnloop.co.uk", "LearnLoop", "/?createSessionSeries")
      },
      hideComponent: function(comp){
        if (comp == "all") { for (let c in this.show) this.hideComponent(c) }
        else { this.show[comp] = false }
      },
      launchFeedback: function(args){
        let path = (args) ? "/feedback/?"+args : "/feedback"
        window.location.assign(path)
      },
      launchInteract: function(args){
        let path = (args) ? "/interact/?"+args : "/interact"
        window.location.assign(path)
      }
    },
    mounted: function() {
      this.hideComponent('loader')
      this.showComponent('welcome')
    }
  })