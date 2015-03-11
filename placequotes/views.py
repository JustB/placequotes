# encoding: utf-8

import hashlib
from io import BytesIO
from PIL import Image, ImageDraw, ImageFont
import textwrap
import random

from django.http import HttpResponse, HttpResponseBadRequest
from django.views.decorators.http import etag
from django.core.urlresolvers import reverse
from django.shortcuts import render
from django.core.cache import cache
from django import forms

def get_quote():
    quotes = [
        u"Se la vita ti sorride, ha una paresi. [Paco D'Alcatraz]",
        u"Al gioco sono sfortunatissimo.  Sono l'unico al mondo a cui capita una mano di poker con cinque carte senza che ce ne siano due dello stesso seme. [Woody Allen]",
        u"Chi trova un amico trova un tesoro, ma chi trova un tesoro ha piu' culo degli altri. [Alessandro Lastrucci]",
        u"Ho smesso di fumare.  Vivro' una settimana in piu' e in quella settimana piovera' a dirotto. [Woody Allen]",
        u"Di solito quando parecchia gente si raduna negli stessi posti si tratta di guerra. [Mel Brooks]",
        u"Il mio grado nell'esercito? Ostaggio, in caso di guerra. [Woody Allen]",
        u"Sono stato picchiato, ma mi sono difeso bene.  A uno di quelli gli ho rotto la mano: mi ci e' voluta tutta la faccia, ma ce l'ho fatta. [Woody Allen]",
        u"Prima di offendere qualcuno contate fino a dieci: vi verranno in mente molti piu' insulti. [Laura Liotta]",
        u"Io non so con quali armi sara' combattuta la III Guerra Mondiale, ma so che la IV Guerra Mondiale sara' combattuta con pietre e bastoni. [Albert Einstein]",
        u"Gli italiani perdono le partite di calcio come se fossero guerre e perdono le guerre come se fossero partite di calcio. [Winston Churchill]",
        u"Ti tiro tanti calci in culo che ti faccio cadere tutti i denti! [Renato Pozzetto]",
        u"Combattere per la pace è come fottere per la verginità. [Anonimo]",
        u"Sono già talmente popolare che uno che mi insulta diventa più popolare di me. [Kark Kraus]",
        u"Non riesco a fare traceroute al tuo cervello...  Di', sei collegato?? [Anonimo]",
        u"I coglioni sono molto più di due. [Eros Drusiani]",
        u"Ero un bambino prodigio.  Impiegavo sempre meno di sei mesi per fare i puzzle, anche se sulla scatola c'era scritto \"dai 2 ai 5 anni\". [Claudio Bisio]",
        u"La mente e' come l'ombrello: per funzionare deve essere aperta. [Paolo Poli]",
    ]
    return random.choice(quotes)


def generate_etag(request, width, height):
    content = 'Placeholder: {0} x {1}'.format(width, height)
    return hashlib.sha1(content.encode('utf-8')).hexdigest()


def longestString(lista):
    maximum = index = -1
    for i,s in enumerate(lista):
        if len(s) > maximum:
            maximum = len(s)
            index = i
    return lista[index]

class ImageForm(forms.Form):
    """Form to validate requested placeholder image."""

    height = forms.IntegerField(min_value=1, max_value=2000)
    width = forms.IntegerField(min_value=1, max_value=2000)



    def generate(self, image_format='PNG'):
        """Generate an image of the given type and return as raw bytes."""

        #font = ImageFont.truetype("DroidSans.ttf", 12)
        font = ImageFont.truetype("Lato-Reg.ttf", 14)
        height = self.cleaned_data['height']
        width = self.cleaned_data['width']
        key = '{}.{}.{}'.format(width, height, image_format)
        content = cache.get(key)
        content = None
        if content is None:
            image = Image.new('RGB', (width, height), '#DDD')
            draw = ImageDraw.Draw(image)
            text1 = '{} X {}'.format(width, height)
            text = get_quote()
            width_with_padding = width - 40
            pieces = textwrap.wrap(text, width_with_padding // (draw.textsize('m')[0]))
            textwidth, textheight = draw.textsize(text)
            textleft = (width - draw.textsize(longestString(pieces))[0]) // 2
            texttop = (height - textheight * len(pieces)) // 2
            for line in pieces:
                draw.text((textleft, texttop), line, fill=(0, 0, 0), font=font)
                texttop += textheight+4

            content = BytesIO()
            image.save(content, image_format)
            content.seek(0)
            cache.set(key, content, 60 * 60)
        return content


#@etag(generate_etag)
def placeholder(request, width, height):
    form = ImageForm({'height': height, 'width': width})
    if form.is_valid():
        image = form.generate()
        return HttpResponse(image, content_type='image/png')
    else:
        return HttpResponseBadRequest('Invalid Image Request')


def index(request):
    example = reverse('placeholder', kwargs={'width': 50, 'height': 50})
    context = {
        'example': 'http://placequot.es/image/500x100'
    }
    return render(request, 'home.html', context)
