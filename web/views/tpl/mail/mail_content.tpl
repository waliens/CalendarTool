
--{$boundary}
Content-Type: multipart/alternative;
 boundary="{$boundary_alt}"
--{$boundary_alt}
Content-Type: text/plain; charset="UTF-8"
Content-Transfer-Encoding: 8bit

{$message_txt}

--{$boundary_alt}
Content-Type: text/html; charset="UTF-8"
Content-Transfer-Encoding: 8bit

{$message_html}

--{$boundary_alt}--

--{$boundary}--