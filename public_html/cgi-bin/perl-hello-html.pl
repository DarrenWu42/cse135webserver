#!/usr/bin/perl

print "Cache-Control: no-cache\n";
print "Content-type: text/html\n\n";
print "<html>";
print "<head>";
print "<title>Hello, Perl!</title>";
print "</head>";
print "<body>";

print "<h1 align=center'>Hello, Perl!</h1>";
print "<hr/>";

print "Hello, World!<br/>";

$date = localtime();
print "This program was generated at: $date<br/>";

# IP Address is an environment variable when using CGI
$address = $ENV{REMOTE_ADDR};
print "Your current IP address is: $address<br/>";

print "</body>";
print "</html>";