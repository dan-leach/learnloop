function apiReal(module, route, id, pin, data) {
  return new Promise(function (resolve, reject) {
    let timeoutDuration = 10000;
    setTimeout(function () {
      reject(
        new Error(
          'Your request timed out. Please check your internet connection.'
        )
      );
    }, timeoutDuration);
    let req = new XMLHttpRequest();
    req.open('POST', 'https://dev.learnloop.co.uk/api/');
    req.onload = function () {
      if (req.status == 200) {
        try {
          console.log('API response', JSON.parse(req.response));
        } catch (e) {
          console.log('API response', req.response);
          console.error('Error outputting API response as parsed object', e);
        }
        resolve(JSON.parse(req.response));
      } else {
        try {
          console.error('API error', JSON.parse(req.response));
          reject(JSON.parse(req.response));
        } catch (e) {
          console.error('API error', e, req);
          reject(
            'API error: status ' +
              req.status +
              ' (' +
              req.statusText +
              ') ' +
              req.response
          );
        }
      }
    };
    req.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
    req.send(
      'module=' +
        module +
        '&route=' +
        route +
        '&id=' +
        id +
        '&pin=' +
        pin +
        '&data=' +
        JSON.stringify(data)
    );
    console.log('API request', { module }, { route }, { id }, { pin }, data);
  });
}

function api(module, route, id, pin, data) {
  console.log('API DEV request', { module }, { route }, { id }, { pin }, data);
  return new Promise(function (resolve, reject) {
    setTimeout(() => {
      if (module == 'feedback') {
        if (route == 'fetchDetails') {
          /*resolve({
          id: id,
          title: 'DEV API Title',
          name: 'DEV API Name',
          date: '01/02/2003',
          subsessions: [],
          questions: [],
        });*/
          resolve({
            id: id,
            title: 'DEV API Title',
            name: 'DEV API Name',
            date: '01/02/2003',
            subsessions: [
              {
                id: 'a12345',
                title: 'DEV API subsession title 1',
                name: 'DEV API subsession name 1',
              },
              {
                id: 'b12345',
                title: 'DEV API subsession title 2',
                name: 'DEV API subsession name 2',
              },
            ],
            questions: [],
          });
        }
        //add remaining feedback routes here
      } else if (module == 'interact') {
        if (route == 'fetchDetails') {
          resolve({
            id: id,
            title: 'DEV API Title',
            name: 'DEV API Name',
            facilitatorIndex: 0,
            interactions: [
              {
                id: 'dev1',
                type: 'singleChoice',
                chart: 'doughnut',
                title: 'What is your favourite cheese?',
                options: ['Feta', 'Cheddar', 'Halloumi', 'Gorganzola', 'Gouda'],
                response: '',
                allowMultiple: false,
              },
              {
                id: 'dev2',
                type: 'singleChoice',
                chart: 'bar',
                title: 'What is your training grade?',
                options: [
                  'ST1',
                  'ST2',
                  'ST3',
                  'ST4',
                  'ST5',
                  'ST6',
                  'ST7',
                  'ST8',
                ],
                response: '',
                allowMultiple: true,
              },
              {
                id: 'dev3',
                type: 'singleChoice',
                chart: 'bar',
                title: 'Are you LTFT?',
                options: ['Yes', 'No'],
                response: '',
                allowMultiple: false,
              },
            ],
          });
        } else if (route == 'checkCurrentIndex') {
          resolve({
            id: id,
            facilitatorIndex: 2,
          });
        } else if (route == 'fetchNewSubmissions') {
          resolve({
            id: id,
            newSubmissions: [
              {
                id: '1',
                data: 0,
              },
              {
                id: '2',
                data: 0,
              },
              {
                id: '3',
                data: 2,
              },
              {
                id: '4',
                data: 3,
              },
              {
                id: '5',
                data: 3,
              },
              {
                id: '6',
                data: 4,
              },
            ],
          });
        } else if (route == 'insertSubmission') {
          resolve(true);
        }
        //add remaining interact routes here
      }
      reject('API error:  module or route not found in DEV API');
    }, 1000);
  });
}

export { api };
