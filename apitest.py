#!/usr/bin/python

import json
import urllib2
import cookielib

class JsonClient:
	'''Simple json-rpc client.  It could probably do a lot more, but basically
	you can give it a url in the constructor and then call functions on the
	remote host as though they were in this class.  This class handles cookie
	tracking for the life of the object.  You could probably do more with that
	if you wanted to.'''
	def __init__( self, url ):
		self.__url = url
		self.__cj = cookielib.CookieJar()
		self.__opener = urllib2.build_opener( urllib2.HTTPCookieProcessor(self.__cj) )

	def __getattr__( self, name ):
		def tmpFunc( *args ):
			try:
				r = self.__opener.open( self.__url, json.dumps({'method': name,
					'params': args, 'id': 0} ) )
				res = json.loads( r.read() )
				if res['error'] is not None:
					raise Exception( res['error'] )
				return res['result']
			except urllib2.HTTPError as e:
				print e.read()
				return None
		return tmpFunc

j = JsonClient('http://taskit.localhost/jsonApi')

print 'Functions: '
methods = j.listMethods( False )
mlen = 0
for m in methods:
	if mlen < len(m):
		mlen = len(m)

fmt = '%%%ds - %%s' % (mlen+3)
for m in methods:
	print fmt % (m, j.help( m ))

for t in xrange(5):
	print j.count()
