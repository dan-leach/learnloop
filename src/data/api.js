function apiReal(module, route, id, pin, data) {
  return new Promise(function (resolve, reject) {
    let timeoutDuration = 10000;
    setTimeout(function () {
      reject(
        new Error('Error: API timeout after ' + timeoutDuration / 1000 + 's')
      );
    }, timeoutDuration);
    let req = new XMLHttpRequest();
    req.open('POST', 'api-v4/?' + route);
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
        'route=' +
        route +
        '&id=' +
        id +
        '&pin=' +
        pin +
        '&data=' +
        data
    );
    console.log('API request', { module }, { route }, { id }, { pin }, data);
  });
}

function api(module, route, id, pin, myData) {
  console.log('dev api request:', module, route, id, pin, myData);
  if (module == 'feedback') {
    switch (route) {
      case 'fetchDetails':
        return {
          id: id,
          title: 'Demo feedback session',
          name: 'Dan Leach',
          date: '24/12/2022',
        };
      default:
        return 'route not found';
    }
  } else if (module == 'interact') {
    switch (route) {
      case 'fetchDetails':
        return {
          id: id,
          title: 'Demo interact session',
          name: 'Dan Leach',
        };
      default:
        return 'route not found';
    }
  } else {
    return 'module not found';
  }
}

export { api };
