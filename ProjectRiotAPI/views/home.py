import re
from django.shortcuts import render
from django.http import HttpResponse
from django.http import JsonResponse
import requests
def index(request):

    apiKey = 'RGAPI-7724d609-5e15-4357-a305-b6a364af3670'
    requestUrl = 'https://euw1.api.riotgames.com/lol/summoner/v4/summoners/by-name/starlon'

    response =requests.get(requestUrl, headers={"X-Riot-Token":apiKey})
    
    return JsonResponse(response.json())