
import sublime, sublime_plugin
import urllib
import urllib2
import threading

class InsertPhraseAPICall(threading.Thread):
    def __init__(self, timeout, phrase_key, phrase_content, request_base_url):
        self.request_base_url = request_base_url
        self.phrase_key = phrase_key
        self.phrase_content = phrase_content
        self.timeout = timeout  
        self.result = None
        threading.Thread.__init__(self)

    def run(self):
        try:
            data = urllib.urlencode({
                'phrase_key' : self.phrase_key,
                'phrase_content' : self.phrase_content,
                # hard-coded here
                'module_name' : 'ynnews'
                })
            request = urllib2.Request(self.request_base_url, data,
                headers={"User-Agent": "Sublime Prefixr"})
            http_file = urllib2.urlopen(request, timeout=self.timeout)
            self.result = http_file.read()
            print(self.result)
            return

        except (urllib2.HTTPError) as (e):
            err = '%s: HTTP error %s contacting API' % (__name__, str(e.code))
        except (urllib2.URLError) as (e):
            err = '%s: URL error %s contacting API' % (__name__, str(e.reason))

        sublime.error_message(err)
        self.result = False

class ExampleCommand(sublime_plugin.TextCommand):
	def run(self, edit, a):
		self.view.insert(edit, 0, "Hello, World!")


class MinhtaCommand(sublime_plugin.TextCommand):
	def run(self, edit, a):
		self.view.insert(edit, 0, 'Minh Hello everyone!')

class InsertOxwallPhraseCommand(sublime_plugin.TextCommand):
    def run(self, edit, phrase_key, phrase_content):
        thread = InsertPhraseAPICall(5000, phrase_key, phrase_content, 'http://localhost/importPhrases/oxwall.php')
        thread.start()


