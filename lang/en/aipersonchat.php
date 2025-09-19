<?php
// This file is part of Moodle - https://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

/**
 * English language strings for AI Person Chat module.
 *
 * @package    mod_aipersonchat
 * @copyright  2025 Yedidia Klein
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['ai_context_prompt'] = 'Based on the information from: {$a->url}

Person: {$a->personname}
Era: {$a->era}

Previous conversation:
{$a->history}

You are {$a->personname}, a historic figure from {$a->era}. Respond to the following message as if you were this person, drawing from your knowledge of their life, personality, and historical context. Keep responses engaging but historically accurate. If the topic is not related to your era or expertise and restricttopic is enabled, politely redirect the conversation back to topics you would know about.

User message: {$a->message}

Please respond as {$a->personname}:';

$string['aipersonchatname'] = 'Activity name';
$string['aipersonchatname_help'] = 'Enter a name for this AI Person Chat activity.';
$string['chat'] = 'Chat';
$string['chatbehavior'] = 'Chat behavior settings';
$string['chatting_with'] = 'Chatting with {$a}';
$string['era'] = 'Era';
$string['imageurl'] = 'Person image URL';
$string['imageurl_help'] = 'Enter a URL to an image of the historic person (portrait, statue, etc.).';
$string['invalidimageurl'] = 'The provided URL does not appear to be a valid image.';
$string['learnmore'] = 'Learn more';
$string['max_messages_reached'] = 'You have reached the maximum number of messages for this activity.';
$string['maxmessages'] = 'Maximum messages per student';
$string['maxmessages_help'] = 'The maximum number of messages each student can send in this activity.';
$string['message_too_long'] = 'Message is too long. Maximum allowed length is {$a} characters.';
$string['modulename'] = 'AI Person Chat';
$string['modulenameplural'] = 'AI Person Chats';
$string['personconfig'] = 'Historic person configuration';
$string['personera'] = 'Time period/era';
$string['personera_help'] = 'The historical time period or era this person lived in (e.g., "Ancient Rome", "Renaissance", "19th century").';
$string['personname'] = 'Historic person name';
$string['personname_help'] = 'Enter the name of the historic person students will chat with.';
$string['personurl'] = 'Information URL';
$string['personurl_help'] = 'A URL with biographical information about this person (e.g., Wikipedia page).';
$string['pluginname'] = 'AI Person Chat';
$string['privacy:metadata:aipersonchat_messages'] = 'Information about messages sent by users in AI Person Chat activities.';
$string['privacy:metadata:aipersonchat_messages:message'] = 'The message content sent by the user.';
$string['privacy:metadata:aipersonchat_messages:response'] = 'The AI response to the user message.';
$string['privacy:metadata:aipersonchat_messages:timecreated'] = 'The time when the message was created.';
$string['privacy:metadata:aipersonchat_messages:timeresponse'] = 'The time when the AI response was generated.';
$string['privacy:metadata:aipersonchat_messages:userid'] = 'The ID of the user who sent the message.';
$string['privacy:metadata:core_ai'] = 'The AI Person Chat activity communicates with the AI subsystem to generate responses to user messages.';
$string['privacy:metadata:core_ai:component'] = 'The component requesting AI generation.';
$string['privacy:metadata:core_ai:contextid'] = 'The context ID where the AI generation is happening.';
$string['privacy:metadata:core_ai:prompttext'] = 'The prompt sent to the AI system, including user message and context.';
$string['privacy:metadata:core_ai:userid'] = 'The ID of the user requesting AI generation.';
$string['restricttopic'] = 'Restrict to person\'s expertise';
$string['restricttopic_help'] = 'When enabled, the AI will try to keep conversations focused on topics the historic person would know about.';
$string['send'] = 'Send';
$string['typemessage'] = 'Type your message here...';

// Capabilities.
$string['aipersonchat:addinstance'] = 'Add a new AI Person Chat activity';
$string['aipersonchat:chat'] = 'Send messages in AI Person Chat';
$string['aipersonchat:view'] = 'View AI Person Chat activity';
