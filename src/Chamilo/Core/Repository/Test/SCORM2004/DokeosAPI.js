var API_1484_11 = new Object();

API_1484_11.Initialize = ChamiloInitialize;
API_1484_11.Terminate = ChamiloTerminate;
API_1484_11.GetValue = ChamiloGetValue;
API_1484_11.SetValue = ChamiloSetValue;
API_1484_11.Commit = ChamiloCommit;
API_1484_11.GetLastError = ChamiloGetLastError;
API_1484_11.GetErrorString = ChamiloGetErrorString;
API_1484_11.GetDiagnostic = ChamiloGetDiagnostic;
API_1484_11.values = new Array();

function ChamiloInitialize()
{
	return "true";
}

function ChamiloTerminate()
{
	return "true";
}

function ChamiloGetValue($variable)
{
	return this.values[$variable];
}

function ChamiloSetValue($variable, $value)
{
	this.values[$variable] = $value;
	alert($variable + ' ' + $value);
	return "true";
}

function ChamiloCommit()
{
	return "true";
}

function ChamiloGetLastError()
{
	return 0;
}

function ChamiloGetErrorString()
{
	return "true";
}

function ChamiloGetDiagnostic()
{
	return "true";
}