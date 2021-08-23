<%@ Language=VBScript %>
<%

sub dolog

dim fs
dim ts
dim cdr
dim fn
dim ev

ev = Request.QueryString("event")
Response.Write("<H3>Event: " & ev & "</H3>")

' open CDR log file
set fs = server.CreateObject("Scripting.FileSystemObject")
fn = Session("cdrfn")
set ts = nothing
on error resume next
set ts = fs.OpenTextFile(fn, 8, true)
if err.number <> 0 then
    Response.Write("OpenTextFile(" & fn & "): ERR=" & Err.number & " (" & Err.source & ")<P>")
    exit sub
end if
on error goto 0

Response.Write("CDR LOG Entry saved to <B>" & fn & "</B><P>")

   
' construct entry
cdr = Session("cdrstamp") & " "

' only log call close records
select case ev
    ' add/remove event names to be logged!!
    case "A:Connect", "B:Connect", "A:Disc", "B:Disc", ""
        rem continue with sub
    case else
        exit sub
end select

for each item in Request.QueryString
    dim value
    value = Request.QueryString(item)
    select case item
        ' add/remove attributes to log
        case "date", "src_cgpn", "src_cdpn", "dst_cgpn", "cause", "event", "aoc", "time", "ref", "msg"
        Response.Write("Tag: <CODE>" & item & " = " & value & "</CODE><BR>")
        cdr = cdr & item & "='" & value & "' "
    end select
next

' write it to file and close
Response.Write("<P>CDR ENTRY(" & cdr & ")<P>")
if (cdr <> "") then
    ts.WriteLine(cdr)
end if
ts.Close

Response.Write("<P>Done.<P>")

end sub

dolog
%>

