function apiX(module, route, id, pin, myData) {
    console.log('api request:',module, route, id, pin, myData)
    fetch('/api', {
        method: 'POST',
        body: JSON.stringify({
            module: module, 
            route: route,
            id: id,
            pin: pin,
            myData: myData
        }),
        headers: {
            'Content-type': 'application/json; charset=UTF-8',
        }
    }).then(function(response) { 
        return response.json()
    }).then(function(data) {
        console.log('api response:', data)
    }).catch(error => console.error('Error:', error)); 
}

function api(module, route, id, pin, myData) {
    console.log('dev api request:',module, route, id, pin, myData)
    if (module == 'feedback'){
        switch(route) {
            case 'fetchDetails':
                return {
                    id: id,
                    title: 'Demo feedback session',
                    name: 'Dan Leach',
                    date: '24/12/2022'
                }
            default:
                return 'route not found'
        }
    } else if (module == 'interact') {
        switch(route) {
            case 'fetchDetails':
                return {
                    id: id,
                    title: 'Demo interact session',
                    name: 'Dan Leach',
                }
            default:
                return 'route not found';
        }
    } else { return 'module not found'}
}

export {api}