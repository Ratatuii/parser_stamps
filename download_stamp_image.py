import os
import requests
from bs4 import BeautifulSoup as bs

url = f'https://filtorg.ru/'

src_img = []
title_img = []

if not os.path.exists('image'):
    os.mkdir('image')

for year in range(1961, 1991):
    count_image_in_year = 0
    r = requests.get(f'{url}marki/sssr/{year}/?items_per_page=256')
    soup = bs(r.text, 'html.parser')
    block = soup.find('div', id='pagination_contents')
    all_image = block.find_all('div', class_='ty-grid-list__image')

    for img in all_image:
        # добавим в списки ссылки и описание на будущее для БД
        src_img.append(img.find('a').get('href'))
        title_img.append(
            ' '.join(img.find('img').get('title').strip().split('.')[3:]).replace('2 марки, фото 1', '').replace(
                ', фото 1', '').replace(' 3 марки', '').replace(' 4 марки', '').replace(' 5 марок', ''))

        image_link = img.find('a').get('href')
        download_storage = requests.get(f'{url}{image_link}/?items_per_page=256').text
        download_soup = bs(download_storage, 'html.parser')
        download_link = download_soup.find('img', class_='ty-pict').get('src')
        image_bytes = requests.get(download_link).content
        file_name = img.find('img').get('title').strip().split('.')[2]
        dir_name = img.find('img').get('title').strip().split('.')[0]

        if not os.path.exists(f'image/{dir_name}'):
            os.mkdir(f'image/{dir_name}')
        if os.path.exists(f'image/{dir_name}/{file_name}.jpg'):
            continue
        else:
            with open(f'image/{dir_name}/{file_name}.jpg', 'wb') as file:
                file.write(image_bytes)
        count_image_in_year += 1

    print(f'В {year} году скачено {count_image_in_year} изображений')
print(f'Всего скачано {len(src_img)} изображений марок.')

print('Завершено успешно!')