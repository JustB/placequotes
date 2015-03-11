import hashlib
from io import BytesIO
from PIL import Image, ImageDraw
import textwrap

from django.http import HttpResponse, HttpResponseBadRequest
from django.views.decorators.http import etag
from django.core.urlresolvers import reverse
from django.shortcuts import render
from django.core.cache import cache
from django import forms


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
        height = self.cleaned_data['height']
        width = self.cleaned_data['width']
        key = '{}.{}.{}'.format(width, height, image_format)
        content = cache.get(key)
        content = None
        if content is None:
            image = Image.new('RGB', (width, height))
            draw = ImageDraw.Draw(image)
            text1 = '{} X {}'.format(width, height)
            text = '"Neque porro quisquam est qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit..."' + text1
            width_with_padding = width - 20
            pieces = textwrap.wrap(text, width_with_padding // (draw.textsize('m')[0]))
            textwidth, textheight = draw.textsize(text)
            textleft = (width - draw.textsize(longestString(pieces))[0]) // 2
            texttop = (height - textheight * len(pieces)) // 2
            for line in pieces:
                draw.text((textleft, texttop), line, fill=(255, 255, 255))
                texttop += textheight

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
