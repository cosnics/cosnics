$.fn.myCallbackFunction = function(file, serverResponse)
{
    console.log(file.toSource());
    console.log(serverResponse);
};