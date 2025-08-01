FreeSWITCH stores users in XML configuration.
/usr/local/freeswitch/conf/directory/default/1001.xml

Example of minimal config for user 1001:
password is the password for SIP registration.
Context is default, it should match the context in sofia/internal.

<include>
  <user id="1001">
    <params>
      <param name="password" value="your_password_here"/>
      <param name="vm-password" value="1001"/>
    </params>
    <variables>
      <variable name="toll_allow" value="domestic,international,local"/>
      <variable name="accountcode" value="1001"/>
      <variable name="user_context" value="default"/>
      <variable name="effective_caller_id_name" value="User 1001"/>
      <variable name="effective_caller_id_number" value="1001"/>
      <variable name="outbound_caller_id_name" value="User 1001"/>
      <variable name="outbound_caller_id_number" value="1001"/>
      <variable name="callgroup" value="techsupport"/>
    </variables>
  </user>
</include>


Check profile settings sofia/internal
/usr/local/freeswitch/conf/sip_profiles/internal.xml


Make sure the domain matches the one you use in calls (1001@domain.local).

<include>
  <profile name="internal">
    ...
    <domains>
      <domain name="domain.local"/>
    </domains>
    ...
  </profile>
</include>


Restart FreeSWITCH to apply changes

fs_cli -x "reloadxml"
fs_cli -x "reload mod_sofia"

Or just restart the whole FreeSWITCH:
service freeswitch restart

Register SIP client (test softphone or library)

Use a softphone (e.g. Zoiper, Linphone) or SIP library to register to 1001@domain.local with the password from XML.

Testing calls

    Now you can call sofia/internal/1001@domain.local from ESL commands.

    FreeSWITCH will know where to route the call (to the registered SIP client).


TD: write a mod_xml_curl configuration example and how to implement it in Symfony.
configuring a SIP profile so that everything works out of the box