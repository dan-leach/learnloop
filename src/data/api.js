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
          interactions: [
            {
              id: 'dev1',
              type: 'singleChoice',
              title: 'Choose an option (1)',
              options: ['a', 'b', 'c', 'd', 'e'],
              response: '',
            },
            {
              id: 'dev2',
              type: 'singleChoice',
              title: 'Choose an option (2)',
              options: ['a', 'b', 'c', 'd', 'e'],
              response: '',
            },
            {
              id: 'dev3',
              type: 'singleChoice',
              title: 'Choose an option (3)',
              options: ['a', 'b', 'c', 'd', 'e'],
              response: '',
            },
          ],
        });
      }
      //add remaining interact routes here
    }
    reject('API error:  module or route not found in DEV API');
  });
}

export { api };
