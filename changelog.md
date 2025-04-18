# Changelog

All notable changes to the LearnLoop codebase will be documented in this file.

## [v6.0] - TBC

### Added

- Auto-save during interaction session creation - previously if the page was refreshed while creating a session all the progress would be lost. Now, once the session title, facilitator name and facilitator email are provided the session is created and an email is sent out with session ID and PIN. Any subsequent changes including adding all the slides are autosaved as the edits are made.
- Presenter view - added a presenter view which can be launched from the waiting room slide. It opens in a new tab which can be positioned on a different screen or positioned in a way that it is not shared with the audience. It shows notes for the current slide (which can be added in the slide edit form) and a preview of the next slide.
- Show response options before showing results - when the user responses are hidden (either because the host needs to toggle between ‘content’ and ‘responses’ or because they’ve used the ‘Hide attendee responses until you reveal them’ setting) the response options are shown on the screen alongside a counter that shows how many responses have been received.
- Highlight correct/incorrect answers - response options on true/false and multiple choice slide types can now (optionally) be flagged as correct or incorrect (or neither). The host reveals the correct/incorrect highlighting by clicking a tick icon just above the responses graph.
- Allow editing response options - the text of a multiple choice option can now be edited after creation, rather than needing to be deleted and then recreated with the new wording.
- Create session from template - new interaction sessions can be created using a previous presentation as a template (for example if you wanted to have two similar versions of a session without having to create the second one from scratch). This can now be done using the ‘create from template’ button at the top of the create session page and requires the session ID and PIN for the session you are using as a template.
- Preserve phrases setting for word clouds - Keep this off if you want to separate and aggregate words which might be more suitable for pulling out feelings/themes. Words separated by newlines will now be treated as separate, unless the preserve phrases setting is on in which case they will still appear together but with a space between them.

### Changed

- Preview during interaction session creation - this now allows you to test it properly with a mobile. Any submissions that you make during preview are temporary.
- True/false slide type - removed the ‘single choice’ slide type and replaced it with a true/false slide. The true/false slide type only allows a two options which default to ‘true’ or ‘false’ (although the words can be edited). Functionally, a single choice slide can still be created using the multiple choice slide and then setting the ‘Number of options attendees must select’ maximum to 1.
- Multi-step session creation (and edit) form with help - Creating feedback or interaction sessions now uses a form split across multiple pages. Keeping each page less busy allows contextual guidance to be displayed to help people less familiar with LearnLoop.

## [v5.3] - 2025-01-06

### Changed

- Moved API to separate repo as ExpressJS application

## [v5.2] - 2024-06-20

### Added

- Added word cloud slide type.

## [v5.1] - 2024-06-19

- with thanks to Rosie Spooner for feedback

### Added

- Added image uploader filesize limit notice. API response to attempted oversize upload now includes filesize limit.
- Added image uploader privacy/copyright notice.
- Added changelog

## [v5.0] - 2024-06-17

### Changed

- Client-side codebase rebuild moving from Vue 2 to Vue 3 Composition with build step. No longer supporting internet explorer. The new build is a single page application with various user interface improvements.
- Various fixes relating to Feedback module.

### Added

- New Interaction module added to enable live interactions with attendees.
