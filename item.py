import discord
import os
import requests
import re
import asyncio

TOKEN = 'MTM2NjAxNjg3OTI4OTgyNzM4MQ.GaI45I.U4Ry52ZW4wc-hsA_lAaF3ZFYGmpbOyj6HRNenw'  # Replace this with your bot token
CHANNEL_ID = 1365297460242546719  # Replace with your channel ID
DOWNLOAD_FOLDER = 'downloaded_images'
HTML_FILE = 'items.html'  # Name of the HTML file to be saved

# Ensure the download folder exists
if not os.path.exists(DOWNLOAD_FOLDER):
    os.makedirs(DOWNLOAD_FOLDER)

# Set up intents
intents = discord.Intents.default()
intents.message_content = True
client = discord.Client(intents=intents)

def parse_message_content(content):
    """
    Extract item details from the message content.
    Returns a tuple with name, price, level, reputation, and location.
    """
    try:
        name = re.search(r"Name:\s*(.+)", content).group(1).strip()
        price = re.search(r"Price \(JS/SC\):\s*(.+)", content).group(1).strip()
        level = re.search(r"Required Level:\s*(.+)", content).group(1).strip()
        rep = re.search(r"Reputation:\s*(.+)", content).group(1).strip()
        location = re.search(r"Location:\s*(.+)", content).group(1).strip()
        return name, price, level, location, rep
    except Exception as e:
        print(f"Error parsing message content: {e}")
        return None

async def download_image(url, filename):
    """
    Downloads the image from the given URL and saves it to the specified filename.
    Returns True if the image was downloaded successfully, otherwise False.
    """
    try:
        print(f"Attempting to download image from URL: {url}")
        r = requests.get(url)
        if r.status_code == 200:
            with open(filename, 'wb') as f:
                f.write(r.content)
            return True
        else:
            print(f"Failed to download image, HTTP Status Code: {r.status_code}")
    except Exception as e:
        print(f"Error downloading {url}: {e}")
    return False

@client.event
async def on_ready():
    print(f"Logged in as {client.user}")
    channel = client.get_channel(CHANNEL_ID)
    if channel is None:
        print("Channel not found.")
        return

    html_entries = []
    image_index = 1

    async for msg in channel.history(limit=None, oldest_first=False):
        # Check if the message contains an attachment
        if not msg.attachments:
            print(f"Message ID {msg.id} has no attachments. Skipping.")
            continue

        print(f"Processing message ID {msg.id}...")

        # Parse message content to extract item info
        parsed = parse_message_content(msg.content)
        if not parsed:
            print(f"Message ID {msg.id} doesn't match the expected format. Skipping.")
            continue

        name, price, level, location, rep = parsed
        print(f"Parsed item: {name}, {price}, {level}, {location}, {rep}")

        # Process the attachment and download the image
        attachment = msg.attachments[0]  # Assuming there's only one attachment per message
        print(f"Processing attachment: {attachment.filename}")

        # Ensure file extension is valid and clean the URL
        file_extension = attachment.filename.split('.')[-1].split('?')[0]
        image_filename = f'image_{image_index}.{file_extension}'
        image_path = os.path.join(DOWNLOAD_FOLDER, image_filename)

        # Download the image
        success = await download_image(attachment.url, image_path)
        if not success:
            print(f"Failed to download image from message ID {msg.id}. Skipping.")
            continue

        # Add the HTML entry for this item
        html_entry = f'''
        <div class="item-card">
          <img src="pics/{image_filename}" alt="{name}" class="item-image" />
          <p class="item-name">{name}</p>
          <p class="item-price">{price}</p>
          <p class="item-level">Level {level}</p>
          <p class="item-location">{location}</p>
          <p class="item-reputation">{rep}</p>
        </div>
        '''
        html_entries.append(html_entry)
        image_index += 1

    # If no images were downloaded, don't proceed to save HTML
    if len(html_entries) == 0:
        print("No images were downloaded.")
        return

    # Save the HTML entries to a file
    with open(HTML_FILE, 'w', encoding='utf-8') as f:
        f.write("<html><body>\n")
        f.write('<h1>Item List</h1>\n')
        f.write('\n'.join(html_entries))
        f.write("\n</body></html>")
    print(f"Done. Downloaded {image_index - 1} images and saved HTML.")
    print(f"HTML file saved as {HTML_FILE}.")

client.run(TOKEN)
