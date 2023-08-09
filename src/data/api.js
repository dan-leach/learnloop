function api(module, route, id, pin, data) {
  return new Promise(function (resolve, reject) {
    let timeoutDuration = 10000;
    setTimeout(function () {
      reject(
        new Error('Your request timed out. Please check your internet connection.')
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

export { api };
