# AI Person Chat - Moodle Activity Plugin

A Moodle activity plugin that allows students to have AI-powered conversations with historical figures. Students can chat with famous people from history, learning about their lives, ideas, and historical contexts through interactive dialogue.

## Features

- **Historical Person Configuration**: Set up conversations with any historical figure
- **AI-Powered Responses**: Uses Moodle's AI subsystem to generate contextually appropriate responses
- **Educational Focus**: Responses are historically accurate and educationally valuable
- **Customizable Behavior**: Control conversation topics and message limits
- **Visual Enhancement**: Add images of historical figures via URL
- **Privacy Compliant**: Full privacy API implementation

## Requirements

- **Moodle Version**: 4.5 or later
- **AI Subsystem**: Requires Moodle's AI subsystem to be configured with an AI provider
- **Capabilities**: Standard teacher/student roles with appropriate permissions

## Installation

1. Download or clone this plugin to your Moodle server
2. Place the plugin files in `/path/to/moodle/mod/aipersonchat/`
3. Visit your Moodle site as an administrator
4. Complete the plugin installation process through the admin interface
5. Configure your AI provider in Moodle's AI subsystem (Site administration > AI > AI providers)

## Configuration

### Activity Settings

When creating an AI Person Chat activity, configure:

#### Historic Person Configuration
- **Activity Name**: Descriptive name for the activity
- **Historic Person Name**: Name of the person students will chat with
- **Information URL**: Link to biographical information (e.g., Wikipedia page)
- **Person Image URL**: Direct link to an image of the person
- **Time Period/Era**: Historical context (e.g., "Ancient Rome", "Renaissance")

#### Chat Behavior Settings
- **Restrict to Person's Expertise**: Keep conversations focused on topics the person would know
- **Maximum Messages per Student**: Limit the number of messages each student can send

### AI Provider Setup

This plugin requires Moodle's AI subsystem to be configured with a compatible provider:

1. Go to **Site administration > AI > AI providers**
2. Configure an AI provider (e.g., OpenAI, Anthropic Claude, etc.)
3. Ensure the provider is enabled and properly configured
4. Test the AI functionality before deploying to students

## Usage

### For Teachers

1. **Create Activity**: Add an "AI Person Chat" activity to your course
2. **Configure Person**: Enter details about the historical figure
3. **Set Boundaries**: Configure chat behavior and message limits
4. **Monitor Conversations**: Review student interactions through the activity

### For Students

1. **Access Activity**: Click on the AI Person Chat activity in your course
2. **Start Chatting**: Type messages to begin conversation with the historical figure
3. **Learn Actively**: Ask questions about the person's life, era, and experiences
4. **Stay Focused**: Keep questions relevant to the historical context

## Supported Image Formats

The plugin supports the following image formats for person images:
- JPEG (.jpg, .jpeg)
- PNG (.png)
- GIF (.gif)
- WebP (.webp)
- SVG (.svg)

## Privacy and Data Handling

This plugin implements Moodle's Privacy API and handles user data as follows:

### Data Collected
- **Messages**: Student messages and AI responses
- **Timestamps**: When messages were sent and responses generated
- **User Association**: Links messages to specific users

### Data Usage
- Messages are sent to the configured AI provider for response generation
- Conversation history provides context for better AI responses
- Data is stored according to Moodle's data retention policies

### Data Rights
- Users can request export of their conversation data
- Users can request deletion of their data
- All privacy rights follow Moodle's standard privacy framework

## Development

### File Structure
```
mod/aipersonchat/
├── db/                     # Database definitions and upgrade scripts
├── lang/en/               # English language strings
├── classes/               # PHP classes for AI handling, events, privacy
├── amd/                   # JavaScript modules
├── pix/                   # Plugin icons
├── lib.php                # Core plugin functions
├── mod_form.php           # Activity configuration form
├── view.php               # Main activity view
├── version.php            # Plugin version information
└── README.md              # This file
```

### Key Components

- **AI Handler**: Manages communication with Moodle's AI subsystem
- **Event System**: Logs course module views, messages, and responses
- **Privacy Provider**: Implements data export and deletion
- **External API**: Handles AJAX requests for real-time chat

### Customization

The plugin can be extended or customized by:

1. **Modifying AI Prompts**: Edit the context prompt in language files
2. **Adding Validation**: Extend form validation in `mod_form.php`
3. **Styling Interface**: Customize CSS in `styles.css`
4. **Event Handling**: Add custom event observers

## Troubleshooting

### Common Issues

**AI Not Responding**
- Check AI provider configuration in Site administration
- Verify AI provider API keys and settings
- Test AI functionality with other Moodle AI features

**Images Not Displaying**
- Ensure image URL is publicly accessible
- Check image format is supported
- Verify URL points directly to image file

**Permission Errors**
- Check user has `mod/aipersonchat:chat` capability
- Verify course enrollment and activity availability
- Confirm AI subsystem permissions

### Debug Mode

Enable Moodle debugging to see detailed error messages:
1. Go to Site administration > Development > Debugging
2. Set debug level to "Developer"
3. Check error logs for AI-related issues

## Version History

- **v1.0.0** (2025-09-18): Initial release
  - Basic AI person chat functionality
  - Image URL support (replaced file upload)
  - Privacy API implementation
  - Event logging system

## License

This plugin is licensed under the GNU General Public License v3.0. See the LICENSE file for details.

## Contributing

Contributions are welcome! Please:

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Test thoroughly
5. Submit a pull request

## Support

For support, please:

1. Check this README and Moodle documentation
2. Search existing issues in the repository
3. Create a new issue with detailed information
4. Include Moodle version, AI provider, and error messages

## Credits

- **Author**: Yedidia Klein
- **Copyright**: 2025
- **Moodle Compatibility**: 4.5+

---

*This plugin enhances history education by bringing historical figures to life through AI-powered conversations, making learning more engaging and interactive for students.*
